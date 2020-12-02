define(['module','jquery', 'bootstrap', 'URIjs', 'Readium', 'Spinner', 'storage/Settings', 'i18n/Strings', 'Dialogs', 'ReaderSettingsDialog', 
        'hgn!templates/about-dialog.html', 'hgn!templates/reader-navbar.html', 'hgn!templates/reader-body.html', 'analytics/Analytics', 'screenfull', 'Keyboard', 'EpubReaderMediaOverlays','backend'], 
        function (module, $, bootstrap, URI, Readium, spinner, Settings, Strings, Dialogs, SettingsDialog, AboutDialog, ReaderNavbar, ReaderBody, Analytics, screenfull, Keyboard, EpubReaderMediaOverlays,Backend) {

    var readium, 
        embedded,
        url,
        el = document.documentElement,
        currentDocument;
    var myScroll;

    var currentAnnotations = [];
	
	var backenddata = new Backend();

    var selectSpread = function () {
        var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);

        if(w/h<=1.2||w<1136) {
            readium.reader.updateSettings({
                isSyntheticSpread: false
            });
        }
        else {
            readium.reader.updateSettings({
                isSyntheticSpread: true
            });
        }
    };

    $(window).resize( selectSpread );

    var loadEbook = function (packageDocumentURL, readerSettings, openPageRequest) {
        
        readium.openPackageDocument(packageDocumentURL, function(packageDocument, options){
            currentDocument = packageDocument;
            currentDocument.generateTocListDOM(function(dom){
                loadToc(dom)
            });
            SettingsDialog.updateBookLayout(readium.reader, readerSettings);
            var metadata = options.metadata;
        }, openPageRequest);
        
    };

    var spin = function()
    {
        if (spinner.willSpin || spinner.isSpinning) return;
        
        spinner.willSpin = true;
        
        setTimeout(function()
        {
            if (spinner.stopRequested)
            {
                spinner.willSpin = false;
                spinner.stopRequested = false;
                return;
            }
            spinner.isSpinning = true;
            spinner.spin($('#reading-area')[0]);
            
            spinner.willSpin = false;
            
        }, 100);
    };

    var tocShowHideToggle = function(){
        
        $(document.body).removeClass('hide-ui');
        
        var $appContainer = $('#app-container'),
            hide = $appContainer.hasClass('toc-visible');
        var bookmark;
        if (readium.reader.handleViewportResize && !embedded){
            bookmark = JSON.parse(readium.reader.bookmarkCurrentPage());
        }

        if (hide){
            $appContainer.removeClass('toc-visible');

            setTimeout(function(){ $('#tocButt')[0].focus(); }, 100);
        }
        else{
            $appContainer.addClass('toc-visible');

            if($appContainer.hasClass('eest-visible')) {
                $('#app-container').removeClass('video-visible');
                $('#app-container').removeClass('eest-visible');
            }

            setTimeout(function(){ $('#readium-toc-body button.close')[0].focus(); }, 100);
        }

        if(embedded){
            hideLoop(null, true);
        }else if (readium.reader.handleViewportResize){

            readium.reader.handleViewportResize();
            
            setTimeout(function()
            {
                readium.reader.openSpineItemElementCfi(bookmark.idref, bookmark.contentCFI, readium.reader);
            }, 90);
        }
    };

    var loadToc = function(dom){
        $('script', dom).remove();

        var tocNav;

        var $navs = $('nav', dom);
        Array.prototype.every.call($navs, function(nav){
            if (nav.getAttributeNS('http://www.idpf.org/2007/ops', 'type') == 'toc'){
                tocNav = nav;
                return false;
            }
            return true;
        });

        
        var toc = (tocNav && $(tocNav).html()) || $('body', dom).html() || $(dom).html();
        var tocUrl = currentDocument.getToc();

        if (toc && toc.length)
        {
            if (true) // button wrap, force-enables tab navigation in Safari (links nav is disabled by default on OSX)
            {
                var $toc = $(toc);
                $('a', $toc).each(function(index)
                {
                    $(this).wrap(function()
                    {
                        var $that = $(this);
                        $that.attr("tabindex", "-1");
                        $that.attr("aria-hidden", "true");
                        var href = $that.attr("href");
                        var title = $that.attr("title");
                        var text = $that[0].textContent; //.innerText (CSS display sensitive + script + style tags)
                        var label = text + ((title && title.length) ? " *** " + title : "") + " --- " + href;
                        return "<button tabindex='60' style='border:0;background:none;padding:0;margin:0;' role='link' aria-label='"+label+"' title='"+label+"'></button>";
                    });
                });

                $('#readium-toc-body').append($toc);

                $('#readium-toc-body').on('click', 'button', function(e)
                {
                    $("a", $(this)).trigger("click");
                    return false;
                });
            }
            else
            {
                $('#readium-toc-body').html(toc);
            }
        }

        var _tocLinkActivated = false;
        
        var lastIframe = undefined;
        readium.reader.on(ReadiumSDK.Events.CONTENT_DOCUMENT_LOADED, function ($iframe, spineItem)
        {
            $iframe.attr("title", "EPUB");
            $iframe.attr("aria-label", "EPUB");
            lastIframe = $iframe[0];
        });
        
        readium.reader.on(ReadiumSDK.Events.PAGINATION_CHANGED, function (pageChangeData)
        {
            savePlace();

            if (spinner.isSpinning)
            {
                spinner.stop();
                spinner.isSpinning = false;
            }
            else if (spinner.willSpin)
            {
                spinner.stopRequested = true;
            }
            
            if (!_tocLinkActivated) return;
            _tocLinkActivated = false;
            
try
{
            var iframe = undefined;
            var element = undefined;
            
            var id = pageChangeData.elementId;
            if (!id)
            {
                var bookmark = JSON.parse(readium.reader.bookmarkCurrentPage());

                if (pageChangeData.spineItem)
                {
                    element = readium.reader.getElementByCfi(pageChangeData.spineItem, bookmark.contentCFI,
                        ["cfi-marker", "mo-cfi-highlight"],
                        [],
                        ["MathJax_Message"]);
                    element = element[0];
                    
                    if (element)
                    {
                        iframe = $("#epub-reader-frame iframe")[0];
                        var doc = ( iframe.contentWindow || iframe.contentDocument ).document;
                        if (element.ownerDocument !== doc)
                        {
                            iframe = $("#epub-reader-frame iframe")[1];
                            if (iframe)
                            {
                                doc = ( iframe.contentWindow || iframe.contentDocument ).document;
                                if (element.ownerDocument !== doc)
                                {
                                    iframe = undefined;
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                iframe = $("#epub-reader-frame iframe")[0];
                var doc = ( iframe.contentWindow || iframe.contentDocument ).document;
                element = doc.getElementById(id);
                if (!element)
                {
                    iframe = $("#epub-reader-frame iframe")[1];
                    if (iframe)
                    {
                        doc = ( iframe.contentWindow || iframe.contentDocument ).document;
                        element = doc.getElementById(id);
                        if (!element)
                        {
                            iframe = undefined;
                        }
                    }
                }
            }

            if (!iframe)
            {
                iframe = lastIframe;
            }
            
            if (iframe)
            {
                var toFocus = iframe; //doc.body
                setTimeout(function(){ toFocus.focus(); }, 50);
            }
}
catch (e)
{
    //
}
        });

        $('#readium-toc-body').on('click', 'a', function(e)
        {
            spin();
            
            var href = $(this).attr('href');
            href = tocUrl ? new URI(href).absoluteTo(tocUrl).toString() : href; 

            _tocLinkActivated = true;
			
            readium.reader.openContentUrl(href);
        
            if (embedded){
                $('.toc-visible').removeClass('toc-visible');
                $(document.body).removeClass('hide-ui');
            }
            return false;
        });
        $('#readium-toc-body').prepend('<button tabindex="50" type="button" class="close" data-dismiss="modal" aria-label="'+Strings.i18n_close+' '+Strings.toc+'" title="'+Strings.i18n_close+' '+Strings.toc+'"><span aria-hidden="true">&times;</span></button>');
        $('#readium-toc-body button.close').on('click', function(){
            tocShowHideToggle();
               return false;
        })
    }
    
    var audioPause = function(){
        $('#audioClip').trigger("pause");
    }

    var eestShowHideToggle = function(){
        $(document.body).removeClass('hide-ui');
        var $appContainer = $('#app-container'),
            hide = $appContainer.hasClass('eest-visible');
        var bookmark;
        if (readium.reader.handleViewportResize && !embedded){
            bookmark = JSON.parse(readium.reader.bookmarkCurrentPage());
        }

        if (hide){
            $appContainer.removeClass('eest-visible');
            setTimeout(function(){ $('#eestButt')[0].focus(); }, 100);
        }
        else{
            $appContainer.addClass('eest-visible');
            if($appContainer.hasClass('toc-visible')) {
                $appContainer.removeClass('toc-visible');
            }
            setTimeout(function(){ $('#ee-soundtrack-body button.close')[0].focus(); }, 100);
        }

        if(embedded){
            hideLoop(null, true);
        }else if (readium.reader.handleViewportResize){
            readium.reader.handleViewportResize();
            setTimeout(function()
            {
                readium.reader.openSpineItemElementCfi(bookmark.idref, bookmark.contentCFI, readium.reader);
            }, 90);
        }
    };

    var loadEESoundtrack = function($iframe, spineItem) {
        bookmark = JSON.parse(readium.reader.bookmarkCurrentPage());
        Settings.get(url+bookmark.idref, function(annotations){
            currentAnnotations = [];
            if (annotations!=null){
                for (var i=0; i<annotations.length;i++){
                    readium.reader.addHighlight(bookmark.idref, annotations[i], i, "highlight")
                    currentAnnotations[i] = annotations[i];
                }
                Settings.put(url+bookmark.idref ,currentAnnotations);
            }
            
        });

        var $eeSoundtrackSource = ($iframe.contents().find("head").find('[name="ee-soundtrack"]'));
        var $eeSoundtrackBody = $("#ee-soundtrack-body");
        var $playlist=$("<ul class='ee-playlist'></ul>");
        var looping=false;
        var playthru=false;
        var $nextTrack;

        if (!$eeSoundtrackSource || $eeSoundtrackSource.length == 0){
            $eeSoundtrackSource = ($iframe.contents().find("body").find('[id="ee-soundtrack"]'));
        }

        if($eeSoundtrackSource.length > 0) {
            $eeSoundtrackBody.empty();
            var spineURI = new URI(url+"/OPS/"+spineItem.href);
            var trackURI, absURI, nowPlayingTitle;
            $eeSoundtrackSource.children().each( function() {

                trackURI = new URI($(this).attr("src"));
                absURI = trackURI.absoluteTo(spineURI);

                var pos = absURI.toString().indexOf("..");
                var location = absURI.toString().substring(0, pos)+url+'OPS/'+absURI.toString().substring(pos+3, );

                if($(this).attr("recommended")=="yes") {
                    $playlist.prepend('<h3 class="recommended-listening">Recommended listening:</h3><li src="'+absURI.toString()+'" class="recommended-listening"><h4>'+$(this).text()+'</h4></li>');
                }
                else
                    $playlist.append('<li src="'+absURI.toString()+'"><h4>'+$(this).text()+'</h4></li>');
            });

            var $firstTrack = $playlist.find("li.recommended-listening");

            if($firstTrack.length == 0)
                $firstTrack = $playlist.find("li:first-of-type");

            $firstTrack.addClass("active");

            var nowPlayingTitle = $firstTrack.find("h4").html();

            $eeSoundtrackBody.append($playlist[0].outerHTML);
            $eeSoundtrackBody.prepend('<audio controls id = "audioClip"><source src="'+$firstTrack.attr("src")+'" type="audio/mp3">Your browser does not support the audio element.</audio>');
            $eeSoundtrackBody.prepend('<div id="now-playing"><h2>Now playing:<br />'+nowPlayingTitle+'</h2><button tabindex="1" type="button" class="btn icon-loop"><span class="glyphicon glyphicon-retweet" aria-hidden="true"></span></button><button tabindex="1" type="button" class="btn icon-playthru"><span class="glyphicon glyphicon-sort-by-order" aria-hidden="true"></span></button></div>');

            var audioElement = $eeSoundtrackBody.find("audio")[0];
            
            $eeSoundtrackBody.find("li").on('click', function() {
                audioElement.pause();
                $eeSoundtrackBody.find("li.active").removeClass("active");
                $eeSoundtrackBody.find("audio source").attr("src", $(this).attr("src"));
                audioElement.load();
                audioElement.play();
                nowPlayingTitle = $(this).find("h4").html();
                $(this).addClass("active");
                $("#now-playing h2").html("Now playing:<br />"+nowPlayingTitle);
            });

            $eeSoundtrackBody.find(".icon-loop").on('click', function(){
                looping = !looping;
                if(looping)
                    playthru = false;
                if(looping)
                    $(this).addClass("active");
                else
                    $(this).removeClass("active");

                if(playthru)
                    $(".icon-playthru").addClass("active");
                else
                    $(".icon-playthru").removeClass("active");
            });

            // playthru playback
            $eeSoundtrackBody.find(".icon-playthru").on('click', function(){
                playthru = !playthru;

                if(playthru)
                    looping = false;

                if(playthru)
                    $(this).addClass("active");
                else
                    $(this).removeClass("active");

                if(looping)
                    $(".icon-loop").addClass("active");
                else
                    $(".icon-loop").removeClass("active");
            });

            audioElement.addEventListener('ended', function() {
                if(looping) {
                    this.currentTime = 0;
                    this.play();
                }
                if(playthru) {
                    this.pause();

                    $nextTrack = $eeSoundtrackBody.find("li:contains('"+nowPlayingTitle+"')").next("li");

                    if($nextTrack.length==0) 
                        $nextTrack = $eeSoundtrackBody.find("li:first");

                    $eeSoundtrackBody.find("audio source").attr("src", $nextTrack.attr("src"));
                    $eeSoundtrackBody.find("li.active").removeClass("active");

                    nowPlayingTitle = $nextTrack.find("h4").html();
                    $nextTrack.addClass("active");

                    $("#now-playing h2").html("Now playing:<br />"+nowPlayingTitle);

                    this.load();
                    this.play();
                }
            }, false);

            $eeSoundtrackBody.prepend('<button tabindex="50" type="button" class="close" data-dismiss="modal" aria-label="'+Strings.i18n_close+' '+Strings.toc+'" title="'+Strings.i18n_close+' '+Strings.toc+'"><span aria-hidden="true">&times;</span></button>');
            $eeSoundtrackBody.find("button.close").on('click', eestShowHideToggle);
             $eeSoundtrackBody.find("button.close").on('click', audioPause);
            $("#eestButt").show();
        }
        else {
            $('#app-container').removeClass('video-visible');
            $('#app-container').removeClass('eest-visible');

            setTimeout(function(){ $('#eestButt')[0].focus(); }, 100);

            $("#eestButt").hide();

        }
    }
    
    var videoPause = function(){
        $('#videoClip').get(0).pause();
    }


    var videoShowHideToggle = function(){
        
        $(document.body).removeClass('hide-ui');
        
        var $appContainer = $('#app-container'),
            hide = $appContainer.hasClass('video-visible');
        var bookmark;
        if (readium.reader.handleViewportResize && !embedded){
            bookmark = JSON.parse(readium.reader.bookmarkCurrentPage());
        }

        if (hide){
            $('#app-container').removeClass('video-visible');
            $('#app-container').removeClass('eest-visible');

            setTimeout(function(){ $('#videoButt')[0].focus(); }, 100);
        }
        else{
            $appContainer.addClass('video-visible');

            if($appContainer.hasClass('toc-visible')) {
                $appContainer.removeClass('toc-visible');
            }

            setTimeout(function(){ $('#ee-videotrack-body button.close')[0].focus(); }, 100);
        }

        if(embedded){
            hideLoop(null, true);
        }else if (readium.reader.handleViewportResize){

            readium.reader.handleViewportResize();
            
            setTimeout(function()
            {
                readium.reader.openSpineItemElementCfi(bookmark.idref, bookmark.contentCFI, readium.reader);
            }, 90);
        }
    };

    var loadEEVideotrack = function($iframe, spineItem) {
        bookmark = JSON.parse(readium.reader.bookmarkCurrentPage());
        Settings.get(url+bookmark.idref, function(annotations){
            currentAnnotations = [];
            if (annotations!=null){
                for (var i=0; i<annotations.length;i++){
                    readium.reader.addHighlight(bookmark.idref, annotations[i], i, "highlight")
                    currentAnnotations[i] = annotations[i];
                }
                Settings.put(url+bookmark.idref ,currentAnnotations);
            }
            
        });

        var $eeVideotrackSource = ($iframe.contents().find("head").find('[name="ee-videotrack"]'));
        var $eeVideotrackBody = $("#ee-videotrack-body");
        var $playlist=$("<ul class='ee-playlist'></ul>");
        var looping=false;
        var playthru=false;
        var $nextTrack;

        if (!$eeVideotrackSource || $eeVideotrackSource.length == 0){
            $eeVideotrackSource = ($iframe.contents().find("body").find('[id="ee-videotrack"]'));
        }

        if($eeVideotrackSource.length > 0) {
            $eeVideotrackBody.empty();
            var spineURI = new URI(url+"/OPS/"+spineItem.href);
            var trackURI, absURI, nowPlayingTitle1;
            $eeVideotrackSource.children().each( function() {

                trackURI = new URI($(this).attr("src"));
                absURI = trackURI.absoluteTo(spineURI);

                var pos = absURI.toString().indexOf("..");
                var location = absURI.toString().substring(0, pos)+url+'OPS/'+absURI.toString().substring(pos+3, );

                if($(this).attr("recommended")=="yes") {
                    $playlist.prepend('<h3 class="recommended-listening">Recommended listening:</h3><li src="'+absURI.toString()+'" class="recommended-listening"><h4>'+$(this).text()+'</h4></li>');
                }
                else
                    $playlist.append('<li src="'+absURI.toString()+'"><h4>'+$(this).text()+'</h4></li>');
            });

            var $firstTrack = $playlist.find("li.recommended-listening");

            if($firstTrack.length == 0)
                $firstTrack = $playlist.find("li:first-of-type");

            $firstTrack.addClass("active");

            var nowPlayingTitle1 = $firstTrack.find("h4").html();

            $eeVideotrackBody.append($playlist[0].outerHTML);
            $eeVideotrackBody.prepend('<video controls id = "videoClip"><source src="'+$firstTrack.attr("src")+'" type="video/mp4">Your browser does not support the video element.</video>');
            $eeVideotrackBody.prepend('<div id="now-playing"><h2>Now playing:<br />'+nowPlayingTitle1+'</h2><button tabindex="1" type="button" class="btn icon-loop"><span class="glyphicon glyphicon-retweet" aria-hidden="true"></span></button><button tabindex="1" type="button" class="btn icon-playthru"><span class="glyphicon glyphicon-sort-by-order" aria-hidden="true"></span></button></div>');

            var videoElement = $eeVideotrackBody.find("video")[0];
            
            $eeVideotrackBody.find("li").on('click', function() {
                videoElement.pause();
                $eeVideotrackBody.find("li.active").removeClass("active");
                $eeVideotrackBody.find("video source").attr("src", $(this).attr("src"));
                videoElement.load();
                videoElement.play();
                nowPlayingTitle1 = $(this).find("h4").html();
                $(this).addClass("active");
                $("#now-playing h2").html("Now playing:<br />"+nowPlayingTitle1);
            });

            $eeVideotrackBody.find(".icon-loop").on('click', function(){
                looping = !looping;
                if(looping)
                    playthru = false;
                if(looping)
                    $(this).addClass("active");
                else
                    $(this).removeClass("active");

                if(playthru)
                    $(".icon-playthru").addClass("active");
                else
                    $(".icon-playthru").removeClass("active");
            });

            // playthru playback
            $eeVideotrackBody.find(".icon-playthru").on('click', function(){
                playthru = !playthru;

                if(playthru)
                    looping = false;

                if(playthru)
                    $(this).addClass("active");
                else
                    $(this).removeClass("active");

                if(looping)
                    $(".icon-loop").addClass("active");
                else
                    $(".icon-loop").removeClass("active");
            });

            videoElement.addEventListener('ended', function() {
                if(looping) {
                    this.currentTime = 0;
                    this.play();
                }
                if(playthru) {
                    this.pause();

                    $nextTrack = $eeVideotrackBody.find("li:contains('"+nowPlayingTitle1+"')").next("li");

                    if($nextTrack.length==0) 
                        $nextTrack = $eeVideotrackBody.find("li:first");

                    $eeVideotrackBody.find("video source").attr("src", $nextTrack.attr("src"));
                    $eeVideotrackBody.find("li.active").removeClass("active");

                    nowPlayingTitle1 = $nextTrack.find("h4").html();
                    $nextTrack.addClass("active");

                    $("#now-playing h2").html("Now playing:<br />"+nowPlayingTitle1);

                    this.load();
                    this.play();
                }
            }, false);

            $eeVideotrackBody.prepend('<button tabindex="50" type="button" class="close" id = "videoClose" data-dismiss="modal" aria-label="'+Strings.i18n_close+' '+Strings.toc+'" title="'+Strings.i18n_close+' '+Strings.toc+'"><span aria-hidden="true">&times;</span></button>');
            $eeVideotrackBody.find("button.close").on('click', videoShowHideToggle);
            $eeVideotrackBody.find("button.close").on('click', videoPause);
            $("#videoButt").show();
        }
        else {
            $('#app-container').removeClass('video-visible');
            $('#app-container').removeClass('eest-visible');

            setTimeout(function(){ $('#videoButt')[0].focus(); }, 100);

            $("#videoButt").hide();

        }
    }


    var vimeoShowHideToggle = function(){
        
        $(document.body).removeClass('hide-ui');
        
        var $appContainer = $('#app-container'),
            hide = $appContainer.hasClass('vimeo-visible');
        var bookmark;
        if (readium.reader.handleViewportResize && !embedded){
            bookmark = JSON.parse(readium.reader.bookmarkCurrentPage());
        }

        if (hide){
            $('#app-container').removeClass('video-visible');
            $('#app-container').removeClass('vimeo-visible');
            $('#app-container').removeClass('eest-visible');

            setTimeout(function(){ $('#youtubeButt')[0].focus(); }, 100);
        }
        else{
            $appContainer.addClass('vimeo-visible');

            if($appContainer.hasClass('toc-visible')) {
                $appContainer.removeClass('toc-visible');
            }

            setTimeout(function(){ $('#ee-vimeotrack-body button.close')[0].focus(); }, 100);
        }

        if(embedded){
            hideLoop(null, true);
        }else if (readium.reader.handleViewportResize){

            readium.reader.handleViewportResize();
            
            setTimeout(function()
            {
                readium.reader.openSpineItemElementCfi(bookmark.idref, bookmark.contentCFI, readium.reader);
            }, 90);
        }
    };

    var loadEEVimeotrack = function($iframe, spineItem) {
        bookmark = JSON.parse(readium.reader.bookmarkCurrentPage());
        Settings.get(url+bookmark.idref, function(annotations){
            currentAnnotations = [];
            if (annotations!=null){
                for (var i=0; i<annotations.length;i++){
                    readium.reader.addHighlight(bookmark.idref, annotations[i], i, "highlight")
                    currentAnnotations[i] = annotations[i];
                }
                Settings.put(url+bookmark.idref ,currentAnnotations);
            }
            
        });

        var $eeVimeotrackSource = ($iframe.contents().find("head").find('[name="ee-vimeotrack"]'));
        var $eeVimeotrackBody = $("#ee-vimeotrack-body");
        var $playlist=$("<ul class='ee-playlist'></ul>");
        var looping=false;
        var playthru=false;
        var $nextTrack;

        if (!$eeVimeotrackSource || $eeVimeotrackSource.length == 0){
            $eeVimeotrackSource = ($iframe.contents().find("body").find('[id="ee-vimeotrack"]'));
        }

        if($eeVimeotrackSource.length > 0) {
            $eeVimeotrackBody.empty();
            var spineURI = new URI(url+"/OPS/"+spineItem.href);
            var trackURI, absURI, nowPlayingTitle1;
            $eeVimeotrackSource.children().each( function() {

                trackURI = new URI($(this).attr("src"));
                // absURI = trackURI.absoluteTo(spineURI);

                // var pos = absURI.toString().indexOf("..");
                // var location = absURI.toString().substring(0, pos)+url+'OPS/'+absURI.toString().substring(pos+3, );

                if($(this).attr("recommended")=="yes") {
                    $playlist.prepend('<h3 class="recommended-listening">Recommended listening:</h3><li src="'+trackURI+'" class="recommended-listening"><h4>'+$(this).text()+'</h4></li>');
                }
                else
                    $playlist.append('<li src="'+trackURI+'"><h4>'+$(this).text()+'</h4></li>');
            });

            var $firstTrack = $playlist.find("li.recommended-listening");

            if($firstTrack.length == 0)
                $firstTrack = $playlist.find("li:first-of-type");

            $firstTrack.addClass("active");

            var nowPlayingTitle1 = $firstTrack.find("h4").html();

            $eeVimeotrackBody.append($playlist[0].outerHTML);
            $eeVimeotrackBody.prepend('<iframe src="'+$firstTrack.attr("src")+'" id ="vimeoClip"></iframe>');
            $eeVimeotrackBody.prepend('<div id="now-playing"><h2>Now playing:<br />'+nowPlayingTitle1+'</h2><button tabindex="1" type="button" class="btn icon-loop"><span class="glyphicon glyphicon-retweet" aria-hidden="true"></span></button><button tabindex="1" type="button" class="btn icon-playthru"><span class="glyphicon glyphicon-sort-by-order" aria-hidden="true"></span></button></div>');

            var vimeoElement = $eeVimeotrackBody.find("iframe")[0];
            
            $eeVimeotrackBody.find("li").on('click', function() {
                // vimeoElement.pause();
                console.log($(this).attr("src"));
                $eeVimeotrackBody.find("li.active").removeClass("active");
                $eeVimeotrackBody.find("iframe").attr('src', $(this).attr("src"));
                // vimeoElement.load();
                // vimeoElement.play();
                nowPlayingTitle1 = $(this).find("h4").html();
                $(this).addClass("active");
                $("#now-playing h2").html("Now playing:<br />"+nowPlayingTitle1);
            });

            $eeVimeotrackBody.find(".icon-loop").on('click', function(){
                looping = !looping;
                if(looping)
                    playthru = false;
                if(looping)
                    $(this).addClass("active");
                else
                    $(this).removeClass("active");

                if(playthru)
                    $(".icon-playthru").addClass("active");
                else
                    $(".icon-playthru").removeClass("active");
            });

            // playthru playback
            $eeVimeotrackBody.find(".icon-playthru").on('click', function(){
                playthru = !playthru;

                if(playthru)
                    looping = false;

                if(playthru)
                    $(this).addClass("active");
                else
                    $(this).removeClass("active");

                if(looping)
                    $(".icon-loop").addClass("active");
                else
                    $(".icon-loop").removeClass("active");
            });

            vimeoElement.addEventListener('ended', function() {
                if(looping) {
                    this.currentTime = 0;
                    this.play();
                }
                if(playthru) {
                    this.pause();

                    $nextTrack = $eeVimeotrackBody.find("li:contains('"+nowPlayingTitle1+"')").next("li");

                    if($nextTrack.length==0) 
                        $nextTrack = $eeVimeotrackBody.find("li:first");

                    $eeVimeotrackBody.find("iframe").attr("src", $nextTrack.attr("src"));
                    $eeVimeotrackBody.find("li.active").removeClass("active");

                    nowPlayingTitle1 = $nextTrack.find("h4").html();
                    $nextTrack.addClass("active");

                    $("#now-playing h2").html("Now playing:<br />"+nowPlayingTitle1);

                    this.load();
                    this.play();
                }
            }, false);

            $eeVimeotrackBody.prepend('<button tabindex="50" type="button" class="close" id = "vimeoClose" data-dismiss="modal" aria-label="'+Strings.i18n_close+' '+Strings.toc+'" title="'+Strings.i18n_close+' '+Strings.toc+'"><span aria-hidden="true">&times;</span></button>');
            $eeVimeotrackBody.find("button.close").on('click', vimeoShowHideToggle);
            // $eeVimeotrackBody.find("button.close").on('click', vimeoPause);
            $("#youtubeButt").show();
        }
        else {
            $('#app-container').removeClass('video-visible');
            $('#app-container').removeClass('vimeo-visible');
            $('#app-container').removeClass('eest-visible');

            setTimeout(function(){ $('#youtubeButt')[0].focus(); }, 100);

            $("#youtubeButt").hide();

        }
    }

    var toggleFullScreen = function(){
        
        if (!screenfull.enabled) return;
        console.log('full screen');
        // screenfull.request();
        screenfull.toggle();
        
        setTimeout(function()
        {
            if (screenfull.isFullscreen)
            {
                $('#buttFullScreenOn').removeAttr("accesskey");
                $('#buttFullScreenOff').attr("accesskey", Keyboard.accesskeys.FullScreenToggle);
            }
            else
            {
                $('#buttFullScreenOff').removeAttr("accesskey");
                $('#buttFullScreenOn').attr("accesskey", Keyboard.accesskeys.FullScreenToggle);
            }
        },200);
    }

    var hideUI = function(){
        hideTimeoutId = null;
        // don't hide it toolbar while toc open in non-embedded mode
        if (!embedded && $('#app-container').hasClass('toc-visible')){
            return;
        }
        
        var navBar = document.getElementById("app-navbar");
        if (document.activeElement) {
            var within = jQuery.contains(navBar, document.activeElement);
            if (within) return;
        }
        
        var $navBar = $(navBar);
        // BROEKN! $navBar.is(':hover')
        var isMouseOver = $navBar.find(':hover').length > 0;
        if (isMouseOver) return;
        
        if ($('#audioplayer').hasClass('expanded-audio')) return;

        $(document.body).addClass('hide-ui');
    }

    var hideTimeoutId;

    var hideLoop = function(e, immediate){

        if (hideTimeoutId){
            window.clearTimeout(hideTimeoutId);
            hideTimeoutId = null;
        }
        if (!$('#app-container').hasClass('toc-visible') && $(document.body).hasClass('hide-ui')){
            $(document.body).removeClass('hide-ui');
        }
        if (immediate){
            hideUI();
        }
        else{
            hideTimeoutId = window.setTimeout(hideUI, 4000);
        }
    }
    
    var savePlace = function(){
        Settings.put(url, readium.reader.bookmarkCurrentPage(), $.noop);
    }

    var nextPage = function () {

        readium.reader.openPageRight();
        return false;
    };

    var prevPage = function () {

        readium.reader.openPageLeft();
        return false;
    };
	
	var getBookQueryParam = function(){
        var query = window.location.search;
        if (query && query.length){
            query = query.substring(1);
        }
        if (query.length){
            var keyParams = query.split('&');
            for (var x = 0; x < keyParams.length; x++)
            {
                var keyVal = keyParams[x].split('=');
                if (keyVal[0] == 'bookid' && keyVal.length == 2){
                    return keyVal[1];
                }
            }

        }
        return null;
    }

    var installReaderEventHandlers = function(){
        currentAnnotations = [];

        $(".icon-annotations").on("click", function () {
            var aid = currentAnnotations.length;
            
            result = readium.reader.addSelectionHighlight(aid, "highlight");
            if (result && result.CFI){
                currentAnnotations[aid] = result.CFI;
                Settings.put(url+result.idref ,currentAnnotations);
            }
        });
		
		if (window.cordova){
			$(".phonegapshare").show();
			$(".phonegapshare").click(function (e){ 
				var shareurl = backenddata.shareurl + getBookQueryParam();
				window.plugins.socialsharing.share('Check out this book!', 'Embellisher Ereader', '', shareurl);
			});
		}

        var isWithinForbiddenNavKeysArea = function()
        {
            return document.activeElement &&
            (
                document.activeElement === document.getElementById('volume-range-slider')
                || document.activeElement === document.getElementById('time-range-slider')
                || document.activeElement === document.getElementById('rate-range-slider')
                || jQuery.contains(document.getElementById("mo-sync-form"), document.activeElement)
                || jQuery.contains(document.getElementById("mo-highlighters"), document.activeElement)
            )
            ;
        };

        var hideTB = function(){
            if (!embedded && $('#app-container').hasClass('toc-visible')){
                return;
            }
            $(document.body).addClass('hide-ui');
            if (document.activeElement) document.activeElement.blur();
        };
        $("#buttHideToolBar").on("click", hideTB);
        Keyboard.on(Keyboard.ToolbarHide, 'reader', hideTB);

        var showTB = function(){
            $("#aboutButt1")[0].focus();
            $(document.body).removeClass('hide-ui');
            setTimeout(function(){ $("#aboutButt1")[0].focus(); }, 50);
        };
        $("#buttShowToolBar").on("click", showTB);
        Keyboard.on(Keyboard.ToolbarShow, 'reader', showTB);

        Keyboard.on(Keyboard.PagePrevious, 'reader', function(){
            if (!isWithinForbiddenNavKeysArea()) prevPage();
        });
        
        Keyboard.on(Keyboard.PagePreviousAlt, 'reader', prevPage);
        
        $("#previous-page-btn").unbind("click");
        $("#previous-page-btn").on("click", prevPage);

        Keyboard.on(Keyboard.PageNextAlt, 'reader', nextPage);
        
        Keyboard.on(Keyboard.PageNext, 'reader', function(){
            if (!isWithinForbiddenNavKeysArea()) nextPage();
        });

        $("#next-page-btn").unbind("click");
        $("#next-page-btn").on("click", nextPage);


        Keyboard.on(Keyboard.FullScreenToggle, 'reader', toggleFullScreen);
        
        $('#buttFullScreenOn').on('click', toggleFullScreen);
        $('#buttFullScreenOff').on('click', toggleFullScreen);

        var loadlibrary = function()
        {
            $(window).trigger('loadlibrary');
        };

        Keyboard.on(Keyboard.SwitchToLibrary, 'reader', loadlibrary /* function(){setTimeout(, 30);} */ );
        
        $('.icon-library').on('click', function(){
            loadlibrary();
            return false;
        });

        Keyboard.on(Keyboard.TocShowHideToggle, 'reader', function()
        {
            var visible = $('#app-container').hasClass('toc-visible');
            if (!visible)
            {
                tocShowHideToggle();
            }
            else
            {
                setTimeout(function(){ $('#readium-toc-body button.close')[0].focus(); }, 100);
            }
        });
        
        $('.icon-toc').on('click', tocShowHideToggle);

        $('.icon-eest').on('click', eestShowHideToggle);

        $('.icon-videoest').on('click', videoShowHideToggle);

        $('.icon-vimeoest').on('click', vimeoShowHideToggle);

        var setTocSize = function(){
            // var appHeight = $(document.body).height() - $('#app-container')[0].offsetTop;
            var appHeight = $(document.body).height();
            $('#app-container').height(appHeight);
            $('#readium-toc-body').height(appHeight);
        };

        /*var setEestSize = function(){
            var appHeight = $(document.body).height() - $('#app-container')[0].offsetTop;
            $('#app-container').height(appHeight);
            $('#ee-soundtrack-body').height(appHeight);
        };*/

        Keyboard.on(Keyboard.ShowSettingsModal, 'reader', function(){$('#settings-dialog').modal("show")});

        $(window).on('mousemove', hideLoop);
        $(window).on('resize', setTocSize);
        setTocSize();
        // setEestSize();
        hideLoop();

        // captures all clicks on the document on the capture phase. Not sure if it's possible with jquery
        // so I'm using DOM api directly
        document.addEventListener('click', hideLoop, true);

        $("audio").on("play", function() {
            $("audio").not(this).each(function(index, audio) {
                audio.pause();
            });
        });
    };

    var loadReaderUIPrivate = function(){
        $('.modal-backdrop').remove();
        var $appContainer = $('#app-container');
        $appContainer.empty();
        $appContainer.append(ReaderBody({strings: Strings, dialogs: Dialogs, keyboard: Keyboard}));
        $appContainer.append(AboutDialog({strings: Strings, dialogs: Dialogs, keyboard: Keyboard}));
        $('nav').empty();
        $('nav').append(ReaderNavbar({strings: Strings, dialogs: Dialogs, keyboard: Keyboard}));
        installReaderEventHandlers();
        document.title = "Embellisher eReader";


        //load chat
        if (backenddata.userInfo.loggedin == 1){
             
        }
        
        spin();
    }
    
    var loadReaderUI = function (data) {
        
        Keyboard.scope('reader');
        
        url = data.epub;
        Analytics.trackView('/reader');
        embedded = data.embedded;

        if (embedded){
            $(document.body).addClass('embedded');
            currLayoutIsSynthetic = false;
        }
        else{
            currLayoutIsSynthetic = true;
        }
        
        loadReaderUIPrivate();
        
        //because we reinitialize the reader we have to unsubscribe to all events for the previews reader instance
        if(readium && readium.reader) {
            readium.reader.off();
        }
        
        setTimeout(function()
        {
            initReadium(); //async
        }, 0);
    };

    var initReadium = function(){

		//request access to ebook on server
		var okay = true;
		backenddata = new Backend();
		jQuery.ajax({
			 url:    backenddata.mainurl + backenddata.showurl + "?sessionid=" + encodeURIComponent(backenddata.userInfo.sessionid)
				+ "&email=" + encodeURIComponent(backenddata.userInfo.email) + "&file=" + encodeURIComponent(url)
					 ,
			 success: function(result) {
						  if (result.msg!="success"){
							alert(result.msg);
							okay = false;
						  }
					  },
			 async:   false,
			 dataType : 'json'
		});      
		
		if (!okay){
			$(window).triggerHandler('loadlibrary', []);
			return false;
		}
		
		

        Settings.getMultiple(['reader', url], function(settings){

            //var prefix = "http://emrepublishing.com/wp-content/embellisher-ereader";
            var prefix =  (self.location && self.location.origin && self.location.pathname) ? (self.location.origin + self.location.pathname + "/..") : "";
			
            var readerOptions =  {
                el: "#epub-reader-frame", 
                annotationCSSUrl: module.config().annotationCssUrl || (prefix + "/css/annotations.css"),
                enablePageTransitions: true
            };

            var readiumOptions = {
                jsLibRoot: './lib/thirdparty/',
                openBookOptions: {}
            };

            var openPageRequest;
            var bookmark;
            if (settings[url]){
                bookmark = JSON.parse(JSON.parse(settings[url]));
                openPageRequest = {idref: bookmark.idref, elementCfi: bookmark.contentCFI};
            }
           
        

            readium = new Readium(readiumOptions, readerOptions);

            window.navigator.epubReadingSystem.name = "epub-js-viewer";
            window.navigator.epubReadingSystem.version = "0.0.1";

            $(window).on('keyup', function(e)
            {
                if (e.keyCode === 9 || e.which === 9)
                {
                    $(document.body).removeClass('hide-ui');
                }
            });

            readium.reader.addIFrameEventListener('mousemove', function() {
                hideLoop();
            });
            
            readium.reader.addIFrameEventListener('keydown', function(e) {
                Keyboard.dispatch(document.documentElement, e.originalEvent);
            });
            
            readium.reader.addIFrameEventListener('keyup', function(e) {
                Keyboard.dispatch(document.documentElement, e.originalEvent);
            });
            
            readium.reader.addIFrameEventListener('focus', function(e) {
                $(window).trigger("focus");
            });

            SettingsDialog.initDialog(readium.reader);

            $('#settings-dialog').on('hidden.bs.modal', function () {

                Keyboard.scope('reader');

                $(document.body).removeClass('hide-ui');
                setTimeout(function(){ $("#settbutt1").focus(); }, 50);
    
                $("#buttSave").removeAttr("accesskey");
                $("#buttClose").removeAttr("accesskey");
            });
            $('#settings-dialog').on('shown.bs.modal', function () {

                Keyboard.scope('settings');
    
                $("#buttSave").attr("accesskey", Keyboard.accesskeys.SettingsModalSave);
                $("#buttClose").attr("accesskey", Keyboard.accesskeys.SettingsModalClose);
            });


            $('#about-dialog').on('hidden.bs.modal', function () {
                Keyboard.scope('reader');

                $(document.body).removeClass('hide-ui');
                setTimeout(function(){ $("#aboutButt1").focus(); }, 50);
            });
    		$('#about-dialog').on('shown.bs.modal', function(){
                Keyboard.scope('about');
    		});
    
            var readerSettings;
            if (settings.reader){
                readerSettings = JSON.parse(settings.reader);
            }
            if (!embedded){
                readerSettings = readerSettings || SettingsDialog.defaultSettings;
                SettingsDialog.updateReader(readium.reader, readerSettings);
                
                Settings.get('reader', function(json)
                {
                    if (!json)
                    {
                        json = {};
                    }
            
                    for (prop in readerSettings)
                    {
                        if (readerSettings.hasOwnProperty(prop))
                        {
                            json[prop] = readerSettings[prop];
                        }
                    }
                    
                    Settings.put('reader', json);
                });
            }
            else{
                readium.reader.updateSettings({
                    isSyntheticSpread: false
                });
            }


            selectSpread();
            /*readium.reader.updateSettings({
            "scroll": "scroll-doc",
            "syntheticSpread": "single",
            isSyntheticSpread: true
            }); */

            var toggleNightTheme = function(){

                if (!embedded){
            
                    Settings.get('reader', function(json)
                    {
                        if (!json)
                        {
                            json = {};
                        }

                        var isNight = json.theme === "night-theme";
                        json.theme = isNight ? "default-theme" : "night-theme";
                        
                        Settings.put('reader', json);

                        SettingsDialog.updateReader(readium.reader, json);
                    });
                }
            };
            $("#buttNightTheme").on("click", toggleNightTheme);
            Keyboard.on(Keyboard.NightTheme, 'reader', toggleNightTheme);

            readium.reader.on(ReadiumSDK.Events.CONTENT_DOCUMENT_LOAD_START, function($iframe, spineItem) {
                spin();
            });
            
            EpubReaderMediaOverlays.init(readium);
            
            loadEbook(url, readerSettings, openPageRequest);

            readium.reader.on(ReadiumSDK.Events.CONTENT_DOCUMENT_LOADED, loadEESoundtrack);


            readium.reader.on(ReadiumSDK.Events.CONTENT_DOCUMENT_LOADED, loadEEVideotrack);

            readium.reader.on(ReadiumSDK.Events.CONTENT_DOCUMENT_LOADED, loadEEVimeotrack);


            //document.addEventListener('touchmove', function (e) { e.preventDefault(); }, false);



            readium.reader.on("annotationClicked", function(type, idref, cfi, id) {
                readium.reader.removeHighlight(id);
                currentAnnotations[id]="";
                Settings.put(url+idref ,currentAnnotations);
            });
        });
    }

    var unloadReaderUI = function(){

        // needed only if access keys can potentially be used to open a book while a dialog is opened, because keyboard.scope() is not accounted for with HTML access keys :(
        // for example: settings dialogs is open => SHIFT CTRL [B] access key => library view opens with transparent black overlay!
        Dialogs.closeModal();
        Dialogs.reset();
        $('#settings-dialog').modal('hide');
        $('#about-dialog').modal('hide');
        $('.modal-backdrop').remove();
        
        
        Keyboard.off('reader');
        Keyboard.off('settings');

        $('#settings-dialog').off('hidden.bs.modal');
        $('#settings-dialog').off('shown.bs.modal');

        $('#about-dialog').off('hidden.bs.modal');
        $('#about-dialog').off('shown.bs.modal');

        // visibility check fails because iframe is unloaded
        //if (readium.reader.isMediaOverlayAvailable())
        if (readium && readium.reader) // window.push/popstate
            readium.reader.pauseMediaOverlay();
        
        $(window).off('resize');
        $(window).off('mousemove');
        $(window).off('keyup');
        $(window).off('message');
        window.clearTimeout(hideTimeoutId);
        $(document.body).removeClass('embedded');
        document.removeEventListener('click', hideLoop, true);
        $('.book-title-header').remove();

        $(document.body).removeClass('hide-ui');
    }
    
    var applyKeyboardSettingsAndLoadUi = function(data)
    {
        // override current scheme with user options
        Settings.get('reader', function(json)
        {
           Keyboard.applySettings(json);
           
           loadReaderUI(data);
        });
    };
    
    return {
		loadUI : applyKeyboardSettingsAndLoadUi,
        unloadUI : unloadReaderUI
    };
    
});