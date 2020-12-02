
//Creator JS file. copyright van Stein en Groentjes B.V.

var serverurl = "http://bs-solutions.nl/creator/";


function addError(message) {
    $('#alerts').append(
        '<div class="alert alert-danger alert-dismissable">' +
            '<button type="button" class="close" data-dismiss="alert">' +
            '&times;</button>' + message + '</div>');
}

function addSuccess(message) {
    $('#alerts').append(
        '<div class="alert alert-success alert-dismissable">' +
            '<button type="button" class="close" data-dismiss="alert">' +
            '&times;</button>' + message + '</div>');
}


function loadCreator(imagedir, linklist, templatelist,status, templateid){
	//saving a chapter
    var extraplugins = "";
    var familytree = "";
    var filemanagerplugin = "";
    if (status == 2){
        extraplugins = " familytree responsivefilemanager storychoice template";
        familytree = "familytree | storychoice | template";
        filemanagerplugin = "responsivefilemanager";
    }
    console.log('template='+templateid);
    if (templateid == 2){
        extraplugins += " childpopup flipimage ";
        familytree = "storychoice | template | childpopup | flipimage";
    }

    var font_formats= 'Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;AkrutiKndPadmini=Akpdmi-n;Arvo=Arvo;Esteban=EstebanRegular';
    if (templateid==3){
        font_formats= 'Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;AkrutiKndPadmini=Akpdmi-n;Arvo=Arvo;Esteban=EstebanRegular;Castle Sketch=sketch';
    }
    console.log(familytree + "EXTRA:"+extraplugins);
    tinymce.init({
        selector: ".ebook-edit",
        theme: "modern",
        skin: "lightgray",
        force_br_newlines : true,
        force_p_newlines : false,
        font_formats : font_formats,
        forced_root_block : '', // Needed for 3.x
		content_css : "ebooks/"+imagedir+"css/styles.css",
		link_list: linklist,
		entity_encoding : "numeric",
		valid_elements : ""
+"a[accesskey|charset|class|coords|dir<ltr?rtl|href|hreflang|id|lang|name"
  +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rel|rev"
  +"|shape<circle?default?poly?rect|style|tabindex|title|target|type],"
+"abbr[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"acronym[class|dir<ltr?rtl|id|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"address[class|align|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"applet[align<bottom?left?middle?right?top|alt|archive|class|code|codebase"
  +"|height|hspace|id|name|object|style|title|vspace|width],"
+"area[accesskey|alt|class|coords|dir<ltr?rtl|href|id|lang|nohref<nohref"
  +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup"
  +"|shape<circle?default?poly?rect|style|tabindex|title|target],"
+"base[href|target],"
+"basefont[color|face|id|size],"
+"bdo[class|dir<ltr?rtl|id|lang|style|title],"
+"big[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"blockquote[cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
  +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
  +"|onmouseover|onmouseup|style|title],"
+"body[alink|background|bgcolor|class|dir<ltr?rtl|id|lang|link|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onload|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|onunload|style|title|text|vlink],"
+"br[class|clear<all?left?none?right|id|style|title],"
+"button[accesskey|class|dir<ltr?rtl|disabled<disabled|id|lang|name|onblur"
  +"|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|tabindex|title|type"
  +"|value],"
+"caption[align<bottom?left?right?top|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"center[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"cite[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"code[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"col[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
  +"|valign<baseline?bottom?middle?top|width],"
+"colgroup[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl"
  +"|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|span|style|title"
  +"|valign<baseline?bottom?middle?top|width],"
+"dd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
+"del[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"dfn[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"dir[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"div[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"dl[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"dt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
+"em/i[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"fieldset[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"font[class|color|dir<ltr?rtl|face|id|lang|size|style|title],"
+"form[accept|accept-charset|action|class|dir<ltr?rtl|enctype|id|lang"
  +"|method<get?post|name|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onreset|onsubmit"
  +"|style|title|target],"
+"frame[class|frameborder|id|longdesc|marginheight|marginwidth|name"
  +"|noresize<noresize|scrolling<auto?no?yes|src|style|title],"
+"frameset[class|cols|id|onload|onunload|rows|style|title],"
+"h1[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"h2[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"h3[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"h4[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"h5[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"h6[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"head[dir<ltr?rtl|lang|profile],"
+"hr[align<center?left?right|class|dir<ltr?rtl|id|lang|noshade<noshade|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|size|style|title|width],"
+"html[dir<ltr?rtl|lang|version],"
+"iframe[align<bottom?left?middle?right?top|class|id"
  +"|longdesc|marginheight|width|height|marginwidth|name|scrolling<auto?no?yes|src|style"
  +"|title],"
+"img[align<bottom?left?middle?right?top|alt|border|class|dir<ltr?rtl|height"
  +"|hspace|id|ismap<ismap|lang|longdesc|name|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|src|style|title|usemap|vspace|width],"
+"input[accept|accesskey|align<bottom?left?middle?right?top|alt"
  +"|checked<checked|class|dir<ltr?rtl|disabled<disabled|id|ismap<ismap|lang"
  +"|maxlength|name|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
  +"|readonly<readonly|size|src|style|tabindex|title"
  +"|type<button?checkbox?file?hidden?image?password?radio?reset?submit?text"
  +"|usemap|value],"
+"ins[cite|class|datetime|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"isindex[class|dir<ltr?rtl|id|lang|prompt|style|title],"
+"kbd[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"label[accesskey|class|dir<ltr?rtl|for|id|lang|onblur|onclick|ondblclick"
  +"|onfocus|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
  +"|onmouseover|onmouseup|style|title],"
+"legend[align<bottom?left?right?top|accesskey|class|dir<ltr?rtl|id|lang"
  +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"li[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title|type"
  +"|value],"
+"link[charset|class|dir<ltr?rtl|href|hreflang|id|lang|media|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|rel|rev|style|title|target|type],"
+"map[class|dir<ltr?rtl|id|lang|name|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"menu[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"meta[content|dir<ltr?rtl|http-equiv|lang|name|scheme],"
+"noframes[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"noscript[class|dir<ltr?rtl|id|lang|style|title],"
+"object[align<bottom?left?middle?right?top|archive|border|class|classid"
  +"|codebase|codetype|data|declare|dir<ltr?rtl|height|hspace|id|lang|name"
  +"|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|standby|style|tabindex|title|type|usemap"
  +"|vspace|width],"
+"ol[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|start|style|title|type],"
+"optgroup[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"option[class|dir<ltr?rtl|disabled<disabled|id|label|lang|onclick|ondblclick"
  +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
  +"|onmouseover|onmouseup|selected<selected|style|title|value],"
+"p[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|style|title],"
+"param[id|name|type|value|valuetype<DATA?OBJECT?REF],"
+"pre/listing/plaintext/xmp[align|class|dir<ltr?rtl|id|lang|onclick|ondblclick"
  +"|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout"
  +"|onmouseover|onmouseup|style|title|width],"
+"q[cite|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"s[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
+"samp[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"script[charset|defer|language|src|type],"
+"select[class|dir<ltr?rtl|disabled<disabled|id|lang|multiple<multiple|name"
  +"|onblur|onchange|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|size|style"
  +"|tabindex|title],"
+"small[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"span[align<center?justify?left?right|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"strike[class|class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title],"
+"strong/b[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"style[dir<ltr?rtl|lang|media|title|type],"
+"sub[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"sup[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title],"
+"table[align<center?left?right|bgcolor|border|cellpadding|cellspacing|class"
  +"|dir<ltr?rtl|frame|height|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|rules"
  +"|style|summary|title|width],"
+"tbody[align<center?char?justify?left?right|char|class|charoff|dir<ltr?rtl|id"
  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
  +"|valign<baseline?bottom?middle?top],"
+"td[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
  +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
  +"|style|title|valign<baseline?bottom?middle?top|width],"
+"textarea[accesskey|class|cols|dir<ltr?rtl|disabled<disabled|id|lang|name"
  +"|onblur|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|onselect"
  +"|readonly<readonly|rows|style|tabindex|title],"
+"tfoot[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
  +"|valign<baseline?bottom?middle?top],"
+"th[abbr|align<center?char?justify?left?right|axis|bgcolor|char|charoff|class"
  +"|colspan|dir<ltr?rtl|headers|height|id|lang|nowrap<nowrap|onclick"
  +"|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown|onmousemove"
  +"|onmouseout|onmouseover|onmouseup|rowspan|scope<col?colgroup?row?rowgroup"
  +"|style|title|valign<baseline?bottom?middle?top|width],"
+"thead[align<center?char?justify?left?right|char|charoff|class|dir<ltr?rtl|id"
  +"|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup|onmousedown"
  +"|onmousemove|onmouseout|onmouseover|onmouseup|style|title"
  +"|valign<baseline?bottom?middle?top],"
+"title[dir<ltr?rtl|lang],"
+"tr[abbr|align<center?char?justify?left?right|bgcolor|char|charoff|class"
  +"|rowspan|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title|valign<baseline?bottom?middle?top],"
+"tt[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
+"u[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress|onkeyup"
  +"|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style|title],"
+"ul[class|compact<compact|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown"
  +"|onkeypress|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover"
  +"|onmouseup|style|title|type],"
+"var[class|dir<ltr?rtl|id|lang|onclick|ondblclick|onkeydown|onkeypress"
  +"|onkeyup|onmousedown|onmousemove|onmouseout|onmouseover|onmouseup|style"
  +"|title]",
		extended_valid_elements : "a[href|target|onclick|class|id|onhover],div[ontouchstart|id|onhover|class|style|onclick|target],iframe[class|id|longdesc|marginheight|width|height|allowfullscreen|webkitallowfullscreen|mozallowfullscreen|marginwidth|name|scrolling<auto?no?yes|src|style|title]",
        plugins: [
            "advlist autolink lists link image media charmap print hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code",
            "insertdatetime nonbreaking save table contextmenu directionality",
            "paste textcolor colorpicker textpattern directionality"+extraplugins
        ],
        toolbar1: "print code | undo redo searchreplace paste | forecolor backcolor | fontselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ",
        toolbar2: familytree + " | styleselect | link  unlink | image media "+filemanagerplugin+" | ltr rtl | visualchars visualblocks",
        image_advtab: true,
        templates: templatelist,
       external_filemanager_path:"/creator/filemanager/",
       filemanager_title:"Responsive Filemanager" ,
        external_plugins: { "filemanager" : "../../filemanager/plugin.min.js"},
        add_unload_trigger: false,
        schema: "html5",
        style_formats_merge: true,
        style_formats: [
            // Your format as described on this page
            {title: "Drop Caps", items: [
              {title: "Drop Cap Big", block: 'p', classes: 'dropcap'},
              {title: "Drop Cap Medium", block: 'p', classes: 'dropcap_med'},
              {title: "Drop Cap Small", block: 'p', classes: 'dropcap_small'},
              //{title: "Header 2", format: "h2"},
              //{title: "Header 3", format: "h3"},
              //{title: "Header 4", format: "h4"},
              //{title: "Header 5", format: "h5"},
              //{title: "Header 6", format: "h6"}
          ]},
        ]
    });

    $('.iframe-btn').fancybox({  
         'width'     : 900,
         'height'     : 600,
         'type'      : 'iframe',
         'autoSize'     : false
    });


}

function loadhome(){
    $('.ddslick').ddslick({  
        onSelected: function(data){  
            $('#hiddentemplate').val(data.selectedData.value);  
        }    
    }); ;
}

