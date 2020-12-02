define(['jquery', 'bootstrap', 'storage/StorageManager', 'storage/Settings', 'EpubLibraryManager', 'i18n/Strings', 'hgn!templates/library-navbar.html', 
		 'hgn!templates/library-body.html','hgn!templates/store-body.html', 'hgn!templates/empty-library.html', 'hgn!templates/library-item.html', 'hgn!templates/store-item.html', 'hgn!templates/details-dialog.html', 'hgn!templates/buy-dialog.html', 'hgn!templates/about-dialog.html', 'hgn!templates/user-dialog.html', 'hgn!templates/details-body.html', 
		 'hgn!templates/add-epub-dialog.html','ReaderSettingsDialog', 'Dialogs', 'workers/Messages', 'analytics/Analytics', 'Keyboard','backend'], 
		 function($, bootstrap, StorageManager, Settings, libraryManager, Strings, LibraryNavbar, LibraryBody, StoreBody, EmptyLibrary, LibraryItem, StoreItem, DetailsDialog, BuyDialog, AboutDialog, UserDialog, DetailsBody, 
		 		  AddEpubDialog, SettingsDialog, Dialogs, Messages, Analytics, Keyboard, Backend){

	var heightRule,
		noCoverRule,
		maxHeightRule,
		detailsDialogStr = DetailsDialog({strings: Strings});
	var backenddata = new Backend();

	var findHeightRule = function(){
		
 		var styleSheet=document.styleSheets[0];          
 		var ii=0;                                        
 		var cssRule;                               
        do {                                             
            if (styleSheet.cssRules) {                    
            	cssRule = styleSheet.cssRules[ii];         
            } else {                                      
            	cssRule = styleSheet.rules[ii];            
            }                                             
            if (cssRule)  {                               
            	if (cssRule.selectorText.toLowerCase()=='.library-item') {          
                    heightRule = cssRule;                    
                } 
                else if (cssRule.selectorText.toLowerCase()=='.library-item img') {          
                    maxHeightRule = cssRule;                    
                }                      
                else if (cssRule.selectorText.toLowerCase() == '.library-item .no-cover'){
                	noCoverRule = cssRule;
                }       
                                                         
            }                                             
            ii++;                                         
        } while (cssRule);                                       
   	}                                                      
   
	
	var setItemHeight = function(){
		var medWidth = 2,
			smWidth = 3,
			xsWidth = 4,
			rowHeight = 0,
			imgWidth = 0,
			scale = 1;

		var winWidth = window.innerWidth;

		if (winWidth >= 992){
			imgWidth = winWidth * (medWidth/12) - 30;
			rowHeight = 1.33 * imgWidth + 80; 
		}
		else if (winWidth >= 768){
			imgWidth = winWidth * (smWidth/12) - 30;
			rowHeight = 1.33 * imgWidth + 80; 
		}
		else{
			imgWidth = winWidth * (xsWidth/12) - 30;
			rowHeight = 1.33 * imgWidth + 40; 
		}
		heightRule.style.height  = rowHeight + 'px';
		scale = imgWidth/300;
		
		noCoverRule.style.width = imgWidth + 'px';
		noCoverRule.style.height = 1.33 * imgWidth + 'px';
		noCoverRule.style.fontSize = 40 * scale + 'px';
		maxHeightRule.style.height = 1.33 * imgWidth + 'px';
		maxHeightRule.style.width = imgWidth + 'px';
	};

	var showDetailsDialog = function(details){
		var bodyStr = DetailsBody({
			data: details,
			strings: Strings
		});

		$('.details-dialog .modal-body').html(bodyStr);
		$('.details-dialog .delete').on('click', function(){
			$('.details-dialog').modal('hide');
			var success = function(){
				libraryManager.retrieveAvailableEpubs(loadLibraryItems);
				Dialogs.closeModal();
			}

			var promptMsg = Strings.i18n_are_you_sure + ' \'' + details.title + '\'';

			Dialogs.showModalPrompt(Strings.delete_dlg_title, promptMsg, 
									Strings.i18n_delete, Strings.i18n_cancel,
									function(){
										
										if (!backenddata.userInfo || backenddata.userInfo.loggedin != 1){
											alert("You can not delete the sample books");
										}else{
											Dialogs.showModalProgress(Strings.delete_progress_title, '');
											Dialogs.updateProgress(100, Messages.PROGRESS_DELETING, details.title, true); 
											//TODO delete epub with id details.rootDir
											console.log("delete epub with id"+details.bookid);
											
											$.ajax({
												url : backenddata.mainurl + backenddata.deletebook,
												type : "POST",
												dataType : "json",
												cache : false,
												data : {
													email : backenddata.userInfo.email,
													sessionid : backenddata.userInfo.sessionid,
													bookid : details.bookid
												},
												success : function (json) {
													if (json['error'] == "LOGIN"){
														//user needs to login again!
														var newinfo = {
															loggedin : 0,
															name : "",
															type : "Reader",
															email : "",
															genre_of_writing : "",
															status : "",
															public_private : "",
															interests : "",
															sessionid : ""
														};
														Dialogs.closeModal()
														backenddata.saveUserInfo(newinfo);
														
													}else{
														//succes, reload the library
														Dialogs.closeModal()
														libraryManager.retrieveAvailableEpubs(loadLibraryItems,[],true);
													}
												}
											});
										}
										
									});
		});
	}

	var showError = function(errorCode){
		Dialogs.showError(errorCode);
	}

	var loadDetails = function(e){
		var $this = $(this);
		var bookinfo = {
			url : $this.attr('data-package'),
			bookid : $this.attr('data-bookid'),
			bookRoot : $this.attr('data-root'),
			rootDir : $this.attr('data-root-dir'),
			noCoverBg : $this.attr('data-no-cover'),
			author : $this.attr('data-author'),
			coverHref : $this.attr('data-coverHref'),
			description : $this.attr('data-desc'),
			title : $this.attr('data-title') ,
			stripe : $this.attr('data-stripe') };


		$('.details-dialog').remove();
        
        $('.details-dialog').off('hidden.bs.modal');
        $('.details-dialog').off('shown.bs.modal');
        
		$('#app-container').append(detailsDialogStr);
        
        $('#details-dialog').on('hidden.bs.modal', function () {
            Keyboard.scope('library');

            setTimeout(function(){ $this.focus(); }, 50);
        });
		$('#details-dialog').on('shown.bs.modal', function(){
            Keyboard.scope('details');
		});
        
        
		$('.details-dialog').modal();
		showDetailsDialog(bookinfo);
		
	}
	
	
	
	
	
	var loadBuy = function(e){
		var $this = $(this);
		var bookinfo = {
			id : $this.attr('data-book'),
			author : $this.attr('data-author'),
			price : $this.attr('data-price'),
			description : $this.attr('data-desc'),
			title : $this.attr('aria-label'),
			coverHref : $this.attr('data-coverHref'),
			stripe : $this.attr('data-stripe'),
			realprice : 0,
			excerpt : $this.attr('data-excerpt')
		};
		floatprice = parseFloat(bookinfo.price);
		if(!isNaN(floatprice)){
    		bookinfo.realprice = parseInt(floatprice * 100);
    	}
		
		console.log(bookinfo.title+bookinfo.realprice);
		
		$('#buy-dialog').remove();
        $('#checkpromocode').off('click');
        $('#customstripebutton').off('click');
        $('#freebuybutton').off('click');

        $('#buy-dialog').off('hidden.bs.modal');
        $('#buy-dialog').off('shown.bs.modal');
        
		$('#app-container').append(BuyDialog({strings: Strings,book:bookinfo}));


        
        $('#buy-dialog').on('hidden.bs.modal', function () {
            Keyboard.scope('library');

            setTimeout(function(){ $this.focus(); }, 50);
        });
		$('#buy-dialog').on('shown.bs.modal', function(){
			var userdata = new Backend();
			if (!userdata.userInfo || userdata.userInfo.loggedin != 1){
				showError(Messages.ERROR_LOGIN);
				$('#buy-dialog').modal('hide');
			}else{
				$('#form-email').val(userdata.userInfo.email);
				$('#form-sessionid').val(userdata.userInfo.sessionid);
			}


            Keyboard.scope('details');
			//testcode pk_test_oVaeihD6PBc3wJDw2KwK3a0n
			var handler = StripeCheckout.configure({
				key: bookinfo.stripe,
				image: bookinfo.coverHref,
				email: userdata.userInfo.email,
				token: function process_checkout(token){
					$("#stripetokeninput").val(token.id);
					var postData = $("#paymentform").serialize();
					var formURL = userdata.mainurl + userdata.buybook;
					$.ajax(
					{
						url : formURL,
						type: "POST",
						data : postData,
						dataType : "json",
						success:function(data, textStatus, jqXHR)
						{
							//data: return data from server
							if (data.msg == "success"){
								$('#buy-dialog').modal('hide');
								libraryManager.retrieveAvailableEpubs(loadLibraryItems,[],true);
								$("#libraryButton").click();
							}else if (data.msg == "LOGIN"){
								showError(Messages.ERROR_LOGIN);
								logoutUser();
								//alert("Your session is expired, please login and try again.");
								$('#buy-dialog').modal('hide');
							}
						},
						error: function(jqXHR, textStatus, errorThrown)
						{
							showError(Messages.ERROR_BUY_CARD);
							$('#buy-dialog').modal('hide');
							//if fails     
						}
					});
				}
			  });
			  
			  $('#checkpromocode').on('click', function(e) {
				// Check what the promo code offers and update the price accordingly
				var bookid = $this.attr('data-book');
				$.ajax(
				{
					url : userdata.mainurl + userdata.checkcoupon,
					type: "POST",
					data : {code: $("#promocode").val(), bookid: bookid},
					dataType : "json",
					success:function(data, textStatus, jqXHR)
					{
						//data: return data from server
						if (data.msg == "success"){
							if (data.free == 1){
								//free ebups for the win!
								$("#freebuybutton").show();
								$('#customstripebutton').hide();
							}else if (data.discount > 0){
								var centprice = bookinfo.realprice;
								bookinfo.realprice = parseInt(centprice * ((100-data.discount) / 100));
								
								$("#bookprice_p").text("Price: $"+bookinfo.realprice/100.0);
							}
						}else{
							alert("Coupon is not valid.");
						}
					},
					error: function(jqXHR, textStatus, errorThrown)
					{
						showError(Messages.ERROR_BUY_CARD);
						$('#buy-dialog').modal('hide');
						//if fails     
					}
				});
				
				e.preventDefault();
			  });

			  $('#customstripebutton').on('click', function(e) {
				// Open Checkout with further options
				handler.open({
				  name: bookinfo.title,
				  description: bookinfo.description,
				  amount: bookinfo.realprice
				});
				e.preventDefault();
			  });

			  // Close Checkout on page navigation
			  $(window).on('popstate', function() {
				handler.close();
			  });
			

			$("#freebuybutton").on('click', function(e){
				//make buy call to server
				$("#freebuybutton").text("Processing..");
				var $thisbuy = $(this);
				var bookid = $this.attr('data-book');
				var price = $this.attr('data-price');
				var coupon = $("#promocode").val();
				//check user data
				
				if (!userdata.userInfo || userdata.userInfo.loggedin != 1){
					showError(Messages.ERROR_LOGIN);
					$('#buy-dialog').modal('hide');
				}else{
					//proceed with checkout
					$.ajax({
						url : userdata.mainurl + userdata.addbook,
						type : "POST",
						dataType : "json",
						cache : false,
						data : {
							email : userdata.userInfo.email,
							sessionid : userdata.userInfo.sessionid,
							bookid : bookid,
							code : coupon
						},
						success : function (json) {
							if (json['error'] == "LOGIN"){
								showError(Messages.ERROR_LOGIN);
								logoutUser();
								$('#buy-dialog').modal('hide');
							}else{
								//succes, reload the library
								$('#buy-dialog').modal('hide');
								libraryManager.retrieveAvailableEpubs(loadLibraryItems,[],true);
								$("#libraryButton").click();
							}
						}
					});
				}
			
			});
		});
        
        
		$('#buy-dialog').modal('show');
		if (bookinfo.realprice == 0){
			$("#paymentform").hide();
			$("#freebuybutton").show();
		}
		return false;
		
	}
	
	
	var loadStoreItems = function(epubs,searchused){
		if (epubs['error'] == "LOGIN"){
			//user is not logged in
			//user needs to login again!
			logoutUser();
			
			console.log("session expired, or logged in elsewhere");
			$("#profile").click();
		}else{
			$('#app-container .library-items').remove();
			$('#app-container').append(StoreBody({}));
			$( "#StoreSearchForm" ).submit(function( event ) {
				event.preventDefault()
				
				var searchterm = $("#srch-term").val();
				if (searchterm.length < 3){
					showError(Messages.ERROR_SEARCH);
					//alert('Search term should be at least 3 characters!');
				}else{
					libraryManager.retrieveStoreEpubs(loadStoreItems,searchterm);
				}
			});
			if (!epubs.length){
				$("#StoreHeader").text(Strings.no_results+"\""+searchused+"\".");
				//$('#app-container .library-items').append(EmptyLibrary({strings: Strings}));
				return;
			}
			
			var count = 0;
			epubs.forEach(function(epub){
				var noCoverBackground = 'images/covers/cover' + ((count++ % 8) + 1) + '.jpg';
				$('.library-items').append(StoreItem({count:{n: count, tabindex:count*2+99}, epub: epub, strings: Strings, noCoverBackground: noCoverBackground}));
			});
			if (searchused!=""){
				$("#StoreHeader").text(Strings.results_for+"\""+searchused+"\":");
				history.pushState({search: searchused}, "Search", "?search="+searchused);
			}
			$(".buyBook").each(function(index){
				var buttonpriceori = $(this).text();
				var buttonprice = parseFloat(buttonpriceori);
				if(!isNaN(buttonprice)){
					$(this).text(Strings.buy_text+Strings.buy_currency+buttonpriceori);
				}
				if (buttonpriceori == ""){
					$(this).text(Strings.free_sample);
				}
			});
			
			$('.buyBook').on('click', loadBuy);

			if (count == 1 && searchused!=""){
				//just one book, open the loadbuy
				$('.buyBook').first().click();
			}
			
		}
	}
	
	var getShareQueryParam = function(){
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
	
	var getSearchQueryParam = function(){
        var query = window.location.search;
        if (query && query.length){
            query = query.substring(1);
        }
        if (query.length){
            var keyParams = query.split('&');
            for (var x = 0; x < keyParams.length; x++)
            {
                var keyVal = keyParams[x].split('=');
                if (keyVal[0] == 'search' && keyVal.length == 2){
                    return keyVal[1];
                }
            }

        }
        return null;
    }


	var loadLibraryItems = function(epubs){
		if (epubs['error'] == "LOGIN"){
			//user is not logged in
			//user needs to login again!
			logoutUser();
			showError(Messages.ERROR_LOGIN);
			console.log("session expired, or logged in elsewhere");
			$('#app-container .library-items').remove();
			$('#app-container').append(LibraryBody({}));
			$('#app-container .library-items').append(EmptyLibrary({strings: Strings}));
			return;
		}else{
			var shareid = getShareQueryParam();
			var searchterm = getSearchQueryParam();
			
			$('#app-container .library-items').remove();
			$('#app-container').append(LibraryBody({}));
			if (!epubs.length){
				$('#app-container .library-items').append(EmptyLibrary({strings: Strings}));
				if (backenddata.userInfo.loggedin == 1){
					$("#empty-message-arrow").show();
					$("#empty-message-arrow2").hide();
					$("#empty-message").show();
					$("#not-logged-in").hide();
				}
				if (shareid){
					alert("Login or register to view this item.");
				}
				if (searchterm){
					alert("Login or register to view this item.");
				}
				
				return;
			}
			
			
			
			var count = 0;
			var found = false;
			epubs.forEach(function(epub){
				if (!found){
					var noCoverBackground = 'images/covers/cover' + ((count++ % 8) + 1) + '.jpg';
					epub.shareurl = backenddata.shareurl;
					epub.ENCshareurl = encodeURIComponent(backenddata.shareurl);
					$('.library-items').append(LibraryItem({count:{n: count, tabindex:count*2+99}, epub: epub, strings: Strings, noCoverBackground: noCoverBackground}));
				}
				if (shareid == epub.id && shareid){
					//open book
					console.log("Opening book id");
					$(window).triggerHandler('readepub', [epub.rootUrl]);
					shareid = null;
					searchterm = null;
					found = true;
				}
			});
			if (window.cordova){
				$('.phonegapshare').show();
				$(".webshare").hide();
			}
			
			$('.details').on('click', loadDetails);
			$('.searchclick').on('click', function(){
				$("#libraryButton").show();
				$("#marketButton").hide();
				libraryManager.retrieveStoreEpubs(loadStoreItems,$(this).text());
				history.pushState({search: $(this).text()}, "Search", "?search="+$(this).text());
			});
			
			if (shareid){
				//shared book is not bought yet, load store with share id
				$("#libraryButton").show();
				$("#marketButton").hide();
				libraryManager.retrieveStoreEpubs(loadStoreItems,shareid);
			}
			if (searchterm){
				$("#libraryButton").show();
				$("#marketButton").hide();
				libraryManager.retrieveStoreEpubs(loadStoreItems,decodeURIComponent(searchterm));
			}
		}
		
	}

	var readClick = function(e){

		var epubUrl = $(this).attr('data-book'); 
		var bookid = $(this).attr('data-bookid');
		$(window).triggerHandler('readepub', [epubUrl]);
		history.pushState({read: bookid}, "Read", "?bookid="+bookid);
		return false;
	}

	var unloadLibraryUI = function(){

        // needed only if access keys can potentially be used to open a book while a dialog is opened, because keyboard.scope() is not accounted for with HTML access keys :(
        Dialogs.closeModal();
        Dialogs.reset();
        $('.modal-backdrop').remove();

        Keyboard.off('library');
        Keyboard.off('settings');

        $('#user-dialog').off('hidden.bs.modal');
        $('#user-dialog').off('shown.bs.modal');
        
        $('#settings-dialog').off('hidden.bs.modal');
        $('#settings-dialog').off('shown.bs.modal');
        
        $('#about-dialog').off('hidden.bs.modal');
        $('#about-dialog').off('shown.bs.modal');
        
        $('#add-epub-dialog').off('hidden.bs.modal');
        $('#add-epub-dialog').off('shown.bs.modal');
        
        $('.details-dialog').off('hidden.bs.modal');
        $('.details-dialog').off('shown.bs.modal');

        $('#buttSaveProfile').off('click');

		$("#buttRegisterProfile").off('click');
		$('#buttLoginProfile').off('click')
		$("#buttLogoutProfile").off('click');
        
		$(window).off('resize');
		$(document.body).off('click');
		$(window).off('storageReady');
		$('#app-container').attr('style', '');
	}

	var promptForReplace = function(originalData, replaceCallback, keepBothCallback){
		Dialogs.showReplaceConfirm(Strings.replace_dlg_title, Strings.replace_dlg_message + ' \'' + originalData.title + '\'' , 
			Strings.replace, Strings.i18n_cancel, Strings.keepboth, replaceCallback, $.noop, keepBothCallback);
	}

	var handleLibraryChange = function(){
		Dialogs.closeModal();
		libraryManager.retrieveAvailableEpubs(loadLibraryItems);
	}

	var handleFileSelect = function(evt){


		var file = evt.target.files[0];
		console.log("importing "+file);
		$('#add-epub-dialog').modal('hide');
		Dialogs.showModalProgress(Strings.import_dlg_title, Strings.import_dlg_message);
		libraryManager.handleZippedEpub({
			file: file,
			overwrite: promptForReplace,
			success: handleLibraryChange, 
			progress: Dialogs.updateProgress,
			error: showError
		});

	}

	var handleDirSelect = function(evt){
		var files = evt.target.files;
		$('#add-epub-dialog').modal('hide');
		Dialogs.showModalProgress(Strings.import_dlg_title, Strings.import_dlg_message);
		libraryManager.handleDirectoryImport({
			files: files,
			overwrite: promptForReplace,
			success: handleLibraryChange, 
			progress: Dialogs.updateProgress,
			error: showError
		});
	}
	var handleUrlSelect = function(){
		var url = $('#url-upload').val();
		$('#add-epub-dialog').modal('hide');
		Dialogs.showModalProgress(Strings.import_dlg_title, Strings.import_dlg_message);
		libraryManager.handleUrlImport({
			url: url,
			overwrite: promptForReplace,
			success: handleLibraryChange, 
			progress: Dialogs.updateProgress,
			error: showError
		});
	}

	var doMigration = function(){
		Dialogs.showModalProgress(Strings.migrate_dlg_title, Strings.migrate_dlg_message);
		libraryManager.handleMigration({
			success: function(){
				Settings.put('needsMigration', false, $.noop);
				handleLibraryChange();
			}, 
			progress: Dialogs.updateProgress,
			error: showError
		});
	}
	
	function logoutUser(){
		var newinfo = {
			loggedin : 0,
			name : "",
			type : "Reader",
			email : "",
			genre_of_writing : "",
			status : "",
			public_private : "",
			interests : "",
			sessionid : ""
		};
		backenddata.saveUserInfo(newinfo);
		libraryManager.retrieveAvailableEpubs(loadLibraryItems,[],true);
		$("#libraryButton").click();
		$("#libraryButton").hide();
		$("#marketButton").show();
		$("#marketButton").prop('disabled', true);
		
		
	}
	
	function loadUserDialogInfo(userinfo){
		url = "http://www.gravatar.com/avatar/"+md5(userinfo.email)+"?d=" + encodeURIComponent($('.profilepicture').attr("src"));
		$('.profilepicture').attr("src", url);
		$("#usernametitle").text(userinfo.name);
		$("#usernametype").text(userinfo.type);
		$("#usergenre").text(userinfo.genre_of_writing);
		$("#userinterest").text(userinfo.interests);
		$("#useremail").text(userinfo.email);
		if (userinfo.type == "Author"){
			$('.AuthorSetting').show();
		} else {
			$('.AuthorSetting').hide();
		}
	}
    
	var loadLibraryUI = function(){
        
		Dialogs.reset();
        
        Keyboard.scope('library');
        
		Analytics.trackView('/library');
		var $appContainer = $('#app-container');
		$appContainer.empty();
		SettingsDialog.initDialog();
		$appContainer.append(AddEpubDialog({
			canHandleUrl : libraryManager.canHandleUrl(),
			canHandleDirectory : libraryManager.canHandleDirectory(),
            strings: Strings
		}));
		$appContainer.append(AboutDialog({strings: Strings}));
		
		//Test user data (made by Tom Groentjes):
		
// 		console.log(backenddata);

		$appContainer.append(UserDialog({strings: Strings, user: backenddata.userInfo}));
      
		
		
		$('#user-dialog').on('shown.bs.modal', function () {
			if (backenddata.userInfo.type == "Author"){
				$('.AuthorSetting').show();
			} else {
				$('.AuthorSetting').hide();
			}
			$("#buttLoginProfile").text("Login");
			$("#buttLoginProfile").prop('disabled', false);
			$("#buttSaveProfile").prop('disabled', false);
			$("#buttSaveProfile").text("Save");
			console.log("In user profile!");
			url = "https://www.gravatar.com/avatar/"+md5($("#useremail").html())+"?d=" + encodeURIComponent($('.profilepicture').attr("src"));
			$('.profilepicture').attr("src", url);
			
			if (backenddata.userInfo.loggedin != 1){
				$("#tab-user-main").hide();
				$("#footer-userprofile").hide();
				$("#tab-user-login").show();
				$("#footer-userprofile-login").show();
			}

        });
        $('#ShowRegisterButt').on('click', function() {
			$("#tab-user-login").hide();
			$("#footer-userprofile-login").hide();
			$("#tab-user-register").show();
		});
		$('#ResetPasswordButt').on('click', function (e) {
			e.preventDefault();
			window.open(backenddata.applocation + 'admin/resetpassword.php', '_system');
		});
		
		$('#buttEditProfileClose').on('click', function() {
			$("#tab-user-edit").hide();
			$("#tab-user-main").show();
			
			$("#footer-userprofile").show();
			$("#footer-userprofile-edit").hide();
		});
		$('#buttEditProfile').on('click', function() {
			$("#tab-user-edit").show();
			$("#tab-user-main").hide();
			
			$("#footer-userprofile").hide();
			$("#footer-userprofile-edit").show();
		});
		$('#buttSaveProfile').on('click',function(){
			$('#buttSaveProfile').text("Saving..");
			$("#buttSaveProfile").prop('disabled', true);
			var useremail = backenddata.userInfo.email;
			var session = backenddata.userInfo.sessionid;
			var interests = $("#user-interests").val();
			var genre = $("#user-genre").val();
			var public_private = $("#user-public_private").val();
			var newpassword = $("#change-password").val();
			$.post(backenddata.mainurl+backenddata.editprofileurl, {email:useremail,sessionid:session,interests:interests,genre:genre,public_private:public_private, newpassword:newpassword}, function(data, textStatus) {
				$("#tab-user-edit").hide();
				$("#tab-user-main").show();
				
				$("#footer-userprofile").show();
				$("#footer-userprofile-edit").hide();
				$('#buttSaveProfile').text("Save");
				$("#buttSaveProfile").prop('disabled', false);
				backenddata.userInfo.genre_of_writing = genre;
				backenddata.userInfo.interests = interests;
				$("#usergenre").text(genre);
				$("#userinterest").text(interests);
				backenddata.saveUserInfo(backenddata.userInfo);
			});
			
			
		});
		$("#buttRegisterProfile").on('click',function(e){
			e.preventDefault();
			var username = $("#register-name").val();
			var useremail = $("#register-email").val();
			var userpassword = $("#register-password").val();
			var usertype = $("#registertype").val();
			
			if (!$("#registereula").is(':checked')){
				$("#register_error_message").text("Please accept the end user license.");
				$("#buttRegisterProfile").text("Register");
				$("#buttRegisterProfile").prop('disabled', false);
				return;
			}

			$("#buttRegisterProfile").text("Waiting..");
			$("#buttRegisterProfile").prop('disabled', true);
			$.post(backenddata.mainurl+backenddata.registerurl, {username:useremail,password:userpassword,name:username,type:usertype}, function(data, textStatus) {
			  //data contains the JSON object
			  //textStatus contains the status: success, error, etc
			  alert(textStatus);
			//   if (textStatus == "success"){
			
				if (data.msg == "success"){
					console.log('ok');
					$("#tab-user-register").hide();
					$("#tab-user-login").show();
					$("#footer-userprofile-login").show();
					$("#login_error_message").text("Your acount is created, you can now login.");
					
				}else{
					$("#register_error_message").text(data.msg);
				}
				$("#buttRegisterProfile").text("Register");
				$("#buttRegisterProfile").prop('disabled', false);
			//   }else{
			// 	$("#register_error_message").text("Something went wrong, try again.");
			// 	$("#buttRegisterProfile").val("Register");
			// 	$("#buttRegisterProfile").prop('disabled', false);
			//   }
			}, "json");
		});
		$('#buttLoginProfile').on('click',function(e){
			e.preventDefault();
			var useremail = $("#user-email").val();
			var userpassword = $("#user-password").val();
			$("#buttLoginProfile").text("Waiting..");
			$("#buttLoginProfile").prop('disabled', true);
			console.log("CALLING LOGIN");
			$.post(backenddata.mainurl+backenddata.loginurl, {email:useremail,password:userpassword}, function(data, textStatus) {
				
			  //data contains the JSON object
			  //textStatus contains the status: success, error, etc
			  console.log(textStatus);
			  if (textStatus == "success"){
			  
				if (data.loggedin == 1){
					backenddata.saveUserInfo(data);
					$("#tab-user-main").show();
					$("#footer-userprofile").show();
					$("#tab-user-login").hide();
					$("#footer-userprofile-login").hide();
					$("#marketButton").prop('disabled', false);
					//load data in the userprofile
					loadUserDialogInfo(data);
					console.log("get epubs");
					libraryManager.retrieveAvailableEpubs(loadLibraryItems,[],true);
				}else{
					$("#login_error_message").text(data.error);
				}
				$("#buttLoginProfile").text("Login");
				$("#buttLoginProfile").prop('disabled', false);
			  }else{
				$("#login_error_message").text("Something went wrong, try again.");
				$("#buttLoginProfile").val("Login");
				$("#buttLoginProfile").prop('disabled', false);
			  }
			}, "json");
		});
		$("#buttLogoutProfile").on('click',function(){
			$("#tab-user-main").hide();
			$("#footer-userprofile").hide();
			$("#tab-user-login").show();
			$("#footer-userprofile-login").show();

			$("#chatframe").attr("src", "");
			
			var useremail = backenddata.userInfo.email;
			var session = backenddata.userInfo.sessionid;
			$.post(backenddata.mainurl+backenddata.logouturl, {email:useremail,sessionid:session}, function(data, textStatus) {
				//success
				logoutUser();
			});
			
		});
		
		

        $('#about-dialog').on('hidden.bs.modal', function () {
            Keyboard.scope('library');

            setTimeout(function(){ $("#aboutButt1").focus(); }, 50);
        });
		$('#about-dialog').on('shown.bs.modal', function(){
            Keyboard.scope('about');
		});
		
        $('#add-epub-dialog').on('hidden.bs.modal', function () {
            Keyboard.scope('library');

            setTimeout(function(){ $("#addbutt").focus(); }, 50);
        });
		$('#add-epub-dialog').on('shown.bs.modal', function(){
            Keyboard.scope('add');
            
			$('#add-epub-dialog input').val('');

            setTimeout(function(){ $('#closeAddEpubCross')[0].focus(); }, 1000);
		});
		$('#url-upload').on('keyup', function(){
			var val = $(this).val();
			if (val && val.length){
				$('#add-epub-dialog .add-book').prop('disabled', false);
			}
			else{
				$('#add-epub-dialog .add-book').prop('disabled', true);
			}
		});
		$('.add-book').on('click', handleUrlSelect);
		$('nav').empty();
		$('nav').append(LibraryNavbar({strings: Strings, dialogs: Dialogs, keyboard: Keyboard}));
		$('.icon-list-view').on('click', function(){
			$(document.body).addClass('list-view');
            setTimeout(function(){ $('.icon-thumbnails')[0].focus(); }, 50);
		});
		$('.icon-thumbnails').on('click', function(){
			$(document.body).removeClass('list-view');
            setTimeout(function(){ $('.icon-list-view')[0].focus(); }, 50);
		});
		if (backenddata.userInfo.loggedin != 1){
			$("#marketButton").prop('disabled', true);
		}

		/*$("#addbutt").on('click', function() {
			console.log("clicked on add button");
			$("#add-epub-dialog").modal("show");
		});*/
		$("#marketButton").on('click', function () {
			//console.log("clicked on market button");
			history.pushState(null, "EMRE Store", '');
			libraryManager.retrieveStoreEpubs(loadStoreItems);
			$("#libraryButton").show();
			$("#marketButton").hide();
		});
		$("#libraryButton").on('click', function () {
			history.pushState(null, "EMRE Library", 'index.html');
			libraryManager.retrieveAvailableEpubs(loadLibraryItems);
			$("#libraryButton").hide();
			$("#marketButton").show();
		});
		$("#btn_forum").on('click', function (e) {
			e.preventDefault();
			backenddata = new Backend();
			window.open(backenddata.mainurl + backenddata.forum + "?sessionid="+encodeURIComponent(backenddata.userInfo.sessionid) + "&email="+encodeURIComponent(backenddata.userInfo.email), '_system');
		});
		$("#btn_creator").on('click', function (e) {
			e.preventDefault();
			backenddata = new Backend();
			window.open("http://james.com/creator/", '_system');
		});

		
		findHeightRule();
		setItemHeight();
		StorageManager.initStorage(function(){
			libraryManager.retrieveAvailableEpubs(loadLibraryItems);
		}, showError);

        Keyboard.on(Keyboard.ShowSettingsModal, 'library', function(){$('#settings-dialog').modal("show");});

		$(window).trigger('libraryUIReady');
		$(window).on('resize', setItemHeight);

		var setAppSize = function(){
			// var appHeight = $(document.body).height() - $('#app-container')[0].offsetTop;
			var appHeight = $(document.body).height();
            $('#app-container').height(appHeight);
        }
        $(window).on('resize', setAppSize);
        $('#app-container').css('overflowY', 'auto');

        setAppSize();
		$(document.body).on('click', '.read', readClick);
		$('#epub-upload').on('change', handleFileSelect);
		$('#dir-upload').on('change', handleDirSelect);

		document.title = Strings.i18n_emre_library;

        $('#settings-dialog').on('hidden.bs.modal', function () {

            Keyboard.scope('library');

            setTimeout(function(){ $("#settbutt1").focus(); }, 50);
            
            $("#buttSave").removeAttr("accesskey");
            $("#buttClose").removeAttr("accesskey");
        });
        $('#settings-dialog').on('shown.bs.modal', function () {

            Keyboard.scope('settings');
            
            $("#buttSave").attr("accesskey", Keyboard.accesskeys.SettingsModalSave);
            $("#buttClose").attr("accesskey", Keyboard.accesskeys.SettingsModalClose);
        });
        

        //async in Chrome
		Settings.get("needsMigration", function(needsMigration){
			if (needsMigration){
				doMigration();
			}
		});
	}

    var applyKeyboardSettingsAndLoadUi = function(data)
    {
        // override current scheme with user options
        Settings.get('reader', function(json)
        {
           Keyboard.applySettings(json);
           
           loadLibraryUI(data);
        });
    };

	return {
        loadUI : applyKeyboardSettingsAndLoadUi,
        unloadUI : unloadLibraryUI
	};
});
