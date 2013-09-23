$(document).ready(function() {
    $('table tbody tr td *').tooltip({
        html: true,
        placement: 'right'
    });

    $('.openGoogleOne').on('click', function() {
        $('#google-one-term').html($.trim($(this).parent().parent().parent().parent().parent().find('.term').text()));
        $('#mdlGoogleOne').modal('show');
    });

    $('#btn-google-all').on('click', function() {
        $(this).html('<img src=" ' + hostPath + 'img/loading.gif" />');
        if($('#frk-lang-origem').val() != '' && $('#frk-lang-destino').val() != '') {
            $.post(
                    hostPath + 'project/googleAll',
                    {origem: $('#frk-lang-origem').val(), destino: $('#frk-lang-destino').val(), terms: $('#frm-table-terms').serialize()},
                    function(data, status) {
                        $('#btn-google-all').html('Traduzir');

                        for(i in data.terms) {
                            if(i.indexOf('t_') == 0) {
                                var id = i.substr(2);
                                var translated = data.terms[i];

                                $("#" + id).val(translated);
                                $('#mdlGoogleAll').modal('hide');
                            }
                        }
                    }
            ,'json');
        }

        return false;
    });

    $('#btn-google-one').on('click', function() {
        $(this).html('<img src=" ' + hostPath + 'img/loading.gif" />');
        if($('#frk-lang-origem').val() != '' && $('#frk-lang-destino').val() != '') {

            $.post(
                    hostPath + 'project/googleOne',
                    {origem: $('#frk-lang-origem').val(), destino: $('#frk-lang-destino').val(), term: $.trim($('#google-one-term').text())},
                    function(data, status) {
                        $('#btn-google-one').html('Traduzir');
                        $('#' + data.id).val(data.translated);
                        $('#mdlGoogleOne').modal('hide');
                    }
            ,'json');
        }

        return false;
    });

    $('#btn-save-po').on('click', function() {
        $.post(
                hostPath + 'project/exportPo',
                {project: project, lang: lang},
                function(data, status) {
                    if(data.status == 'ok') {
                        // message thats ok
                    }
                }
        ,'json');
    });

    $('#btn-save-mo').on('click', function() {
        $.post(
                hostPath + 'project/exportMo',
                {project: project, lang: lang},
                function(data, status) {
                    if(data.status == 'ok') {
                        // message thats ok
                    }
                }
        ,'json');
    });
});