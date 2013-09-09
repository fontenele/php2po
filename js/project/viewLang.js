$(document).ready(function() {
    $('table tbody tr td *').tooltip({
        html: true,
        placement: 'right'
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

    $('#btn-save-po').on('click', function() {
        var wdw = window.open(hostPath + 'project/exportPo/project/' + project +'/lang/' + lang, 'Exportar ' + lang + '.po');
    });

    $('#btn-save-mo').on('click', function() {
        var wdw = window.open(hostPath + 'project/exportMo/project/' + project +'/lang/' + lang, 'Exportar ' + lang + '.po');
    });
});