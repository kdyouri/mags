SPINNER = '<i class="fa fa-spinner fa-spin fa-4x text-primary"></i>';
DIALOG = '<div class="modal fade xcrud-modal" tabindex="-1" role="dialog" aria-hidden="true">' +
        '<div class="modal-dialog modal-sm">' +
            '<div class="modal-content">' +
                '<div class="modal-header">' +
					'<h5 class="modal-title">Opération en cours...</h5>' +
                    '<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>' +
                '</div>'+
                '<div class="modal-body">' +
                    '<div class="text-center">' +
                        SPINNER +
                    '</div>' +
                '</div>' +
                '<div class="modal-footer">'+
                    '<button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>' +
                '</div>'+
            '</div>' +
        '</div>' +
    '</div>';
LOADING = '<div style="position:fixed;top:0;right:0;bottom:0;left:0;background:rgba(255,255,255,.5);">' +
        '<div style="position:absolute;transform:translate(-50%,-50%);top:50%;left:50%;">' +
            SPINNER +
        '</div>' +
    '</div>';

$(document).on('click', '.xcrud-btn-edit,.xcrud-btn-add,.xcrud-btn-dialog,.xcrud-btn-action-modal', function(e){
    e.preventDefault();
    var $main = Xcrud.getMainContainer(this);
    var url = $(this).attr('href');
    var width = $(this).data('width') || '';
    var callback = $(this).data('callback') || '';
    Xcrud.showDialog($main, {'url' : url, 'width' : width, 'callback' : callback})
});

$(document).on('submit', '.xcrud-modal form', function(e) {
    e.preventDefault();

    var $dialog = $(this).closest('.modal');
    var $main = $dialog.data('xcrudmain');
    var updatecontent = $(this).data('updatecontent') || '';
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: new FormData(this),
        beforeSend: function(){
            Xcrud.startPageLoading();
        },
        success: function(resp){
            if ($(resp).is('.modal-dialog:not(.xcrud-main)')){
                $dialog.html(resp);

                if ($dialog.find('.error-message').length) {
                    // Le temps de voir le Flash ".alert-danger":
                    window.setTimeout(function(){
                        // Défiler vers le premier élement contenant une erreur:
                        let $firstErrorInput = $dialog.find('.error-message').first().closest('.form-group');
                        let scrollY = $firstErrorInput.position().top;

                        $dialog.find('.modal-body').animate({scrollTop: scrollY}, 700);
                    }, 800);
                } else {
                    $dialog.find('input[type=text],input[type=number],input[type=email],textarea,select').not('[disabled]').not('[readonly]').first().focus();
                }
                $dialog.trigger('content.loaded');
            } else {
                if(updatecontent){
                    $dialog.trigger('content.updated', [resp]);
                    $dialog.modal('hide');
                }else{
                    $dialog.modal('hide').on('hidden.bs.modal', function(){
                        Xcrud.updateMainContent($main, resp);
                    });
                }
            }
        },
        error: function(jqxhr){
            toastr.error($(jqxhr.responseText).find('.details>h2').text());
        },
        complete: function(){
            Xcrud.stopPageLoading();
        },
        contentType: false,
        processData: false
    });
});

$(document).on('click', '.xcrud-btn-delete,.xcrud-btn-action', function(e){
    e.preventDefault();

    var msg = $(this).data('msg');
    var url = $(this).attr('href');
    var $main = Xcrud.getMainContainer(this);

    var proceed = function(){
        $.ajax({
            url: url,
            beforeSend: function(){
                Xcrud.startPageLoading();
            },
            success: function(resp){
                Xcrud.updateMainContent($main, resp);
            },
            error: function(jqxhr){
                toastr.error($(jqxhr.responseText).find('.details>h2').text());
            },
            complete: function(){
                Xcrud.stopPageLoading();
            }
        });
    };

    if (msg) {
        bootbox.dialog({
            message: msg,
            title: 'Confirmation',
            buttons: {
                success: {
                    label: 'Oui',
                    className: "btn-left btn-danger",
                    callback: proceed
                },
                danger: {
                    label: 'Non',
                    className: "btn-right btn-success"
                }
            }
        });
    } else {
        proceed();
    }
});

$(document).on('click', '.xcrud-paginate>li>a,.xcrud-sort>a', function(e){
    e.preventDefault();

    var $main = Xcrud.getMainContainer(this);

    $.ajax({
        url: $(this).attr('href'),
        beforeSend: function(){
            Xcrud.startPageLoading();
        },
        success: function(resp){
            Xcrud.updateMainContent($main, resp);
            $('html,body').scrollTop(0);
        },
        error: function(jqxhr){
            toastr.error($(jqxhr.responseText).find('.details>h2').text());
        },
        complete: function(){
            Xcrud.stopPageLoading();
        }
    });
});

var Xcrud = {
    $loading: null,
    startPageLoading: function() {
        Xcrud.$loading = $(LOADING).appendTo('body');
    },
    stopPageLoading: function() {
        if (Xcrud.$loading) Xcrud.$loading.remove();
    },
    getMainContainer: function(child) {
        return $(child).closest('.xcrud-main');
    },
    showDialog: function($main, params){
        var $dialog = $(DIALOG);
        var url = params.url;

        $dialog.data('xcrudmain', $main);

        $dialog.modal('show')
            .on('shown.bs.modal', function(e){
                $.get(url, function(resp){
                    $dialog.html(resp);
                    $dialog.find('input[type=text],input[type=number],input[type=email],textarea,select').not('[disabled]').not('[readonly]').first().focus();
                    $dialog.trigger('content.loaded');
                }).error(function(jqxhr){
                    $dialog.find('.modal-dialog').removeClass('modal-sm');
                    $dialog.find('.modal-title').text('Erreur');
                    $dialog.find('.modal-body').html(jqxhr.responseText);
                });
                $(this).unbind('shown.bs.modal');
            })
            .on('hidden.bs.modal', function(){
                $(this).remove();
            });
    },
    updateMainContent: function($mainContainer, data) {
        $mainContainer.replaceWith(data);
        $mainContainer.trigger('content.updated');
    }
};
