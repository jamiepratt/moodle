
M.form_filepicker = {};


M.form_filepicker.callback = function(params) {
    var Y = this.options.Y;
    var filename = params['file'];
    var html = '<a href="'+params['url']+'">'+filename+'</a>';
    //cannot test for equality of arrays directly
    var startofext = filename.lastIndexOf('.');
    if (-1 !== startofext) {
        var extension = filename.substr(filename.lastIndexOf('.')+1);
        switch (extension) {
            case "jpg" :
            case "png" :
            case "gif" :
                html = '<div class="mform_filepicker_image"><img src="'+params['url']+'" /></div>'
                            + html;
                Y.one('#file_info_'+params['client_id']).setContent(html);
                var imgnode = Y.one('#file_info_'+params['client_id']+' img');
                imgnode.on('load', function(e){
                    M.form_filepicker.constrain_image_size(e.target);
                });
                M.form_filepicker.constrain_image_size(imgnode);
                break;
            case "mp3" :
                var id = 'mform_filepicker_mp3_'+params['client_id'];
                html = '<span class="mediaplugin mediaplugin_mp3" id="'+id+'"></span><br />'+ html;
                Y.one('#file_info_'+params['client_id']).setContent(html);
                M.util.add_audio_player(id, params['url'], true);
                M.util.load_flowplayer();
                break;
            case "f4v" :
            case "flv" :
            case "mp4" :
                var id = 'mform_filepicker_mp3_'+params['client_id'];
                html = '<span class="mediaplugin mediaplugin_flv" id="'+id+'"></span><br />'+ html;
                Y.one('#file_info_'+params['client_id']).setContent(html);
                M.util.add_video_player(id, params['url'], 400, 300, true);
                M.util.load_flowplayer();
                break;
            default :
                Y.one('#file_info_'+params['client_id']).setContent(html);
        }
    } else {
        //no extension in filename!
        Y.one('#file_info_'+params['client_id']).setContent(html);
    }

};

M.form_filepicker.constrain_image_size = function (imgnode) {
    if (!imgnode.get('complete') || imgnode.hasClass('constrained')){
        return;
    }
    var maxsize = {width : '100', height: '100'};
    var reduceby = Math.max(imgnode.get('width') / maxsize.width,
                            imgnode.get('height') / maxsize.height);
    if (reduceby > 1) {
        imgnode.set('width', Math.floor(imgnode.get('width') / reduceby));
    }
    imgnode.addClass('constrained');
}


/**
 * This fucntion is called for each file picker on page.
 */
M.form_filepicker.init = function(Y, options) {
    options.Y = Y;
    options.formcallback = M.form_filepicker.callback;
    if (!M.core_filepicker.instances[options.client_id]) {
        M.core_filepicker.init(Y, options); 
    }
    Y.on('click', function(e, client_id) {
        e.preventDefault();
        M.core_filepicker.instances[client_id].show();
    }, '#filepicker-button-'+options.client_id, null, options.client_id);

    var item = document.getElementById('nonjs-filepicker-'+options.client_id);
    if (item) {
        item.parentNode.removeChild(item);
    }
    item = document.getElementById('filepicker-wrapper-'+options.client_id);
    if (item) {
        item.style.display = '';
    }
};
