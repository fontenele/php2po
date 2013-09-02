$(document).ready(function() {
    $('table tbody tr td *').tooltip({
        html: true,
        placement: 'right'
    });

    $('#btn-google-all').on('click', function() {
        $(this).html('<img src=" ' + hostPath + '/img/loading.gif" />');
        if($('#frk-lang-origem').val() != '' && $('#frk-lang-destino').val() != '') {
            $.post(
                    hostPath + 'project/googleAll',
                    {origem: $('#frk-lang-origem').val(), destino: $('#frk-lang-destino').val(), terms: $('#frm-table-terms').serialize()},
                    function(data, status) {
                        $('#btn-google-all').html('Traduzir');
                        for(i in data.terms) {
                            if(i.indexOf('term_origin_') >= 0) {
                                var id = i.replace('term_origin_', '');
                                eval("var translated = data.terms.term_translate_" + id + ";");
                                $("input[name=term_translate_" + id + "]").val(translated);
                                $('#mdlGoogleAll').modal('hide');
                            }
                        }
                    }
            ,'json');
        }

        return false;
    });
});