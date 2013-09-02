$(document).ready(function() {
    $('#btn-save-lang').on('click', function() {
        if($('#frk-lang').val() != '') {
            $.getJSON(hostPath + 'project/addNewLang/lang/' + $('#frk-lang').val(), function(data) {
                if(data.result == '1') {
                    $('#tbl-idiomas tbody').append('<tr><td><a href="' + hostPath + 'project/viewLang/lang/' + $('#frk-lang').val() + '">' + $('#frk-lang').val() + '</a> <span class="total"></span></td></tr>');
                    $('#mdlNovoIdioma').modal('hide');
                }else{
                    $('#mdlNovoIdioma').modal('hide');
                }
            });
        }
    });

    $('#btn-search-terms').on('click', function() {
        $.ajax({
            type: 'POST',
            url: hostPath + 'project/searchTerms',
            data: {patterns: encodeURIComponent($('#txt-patterns').val()), ignore: encodeURIComponent($('#txt-ignore-dirs').val())},
            dataType: 'json',
            beforeSend: function(XMLHttpRequest) {
                $('#btn-search-terms').html('<img src=" ' + hostPath + '/img/loading.gif" />');
            },
            success: function(data, status) {
                if(status == 'success') {
                    $('#modalTerms').modal('hide');
                    $('#btn-search-terms').html('Buscar');
                    $('#tbl-idiomas tbody tr td .total').html('<span class="badge">' + data.total + '</span>');
                }

                return false;
            }
        });

        return false;
    });
});