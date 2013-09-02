$(document).ready(function() {
    $('#des-caminho-xml').hide();
    $('#des-caminho-xml_aux').hide();

    $('#des-caminho-xml_file').on('click', function() {
        $('#des-caminho-xml').trigger('click');
        return false;
    });

    $('#des-caminho-xml').on('change', function() {

        if($(this).val()) {
            $('#frk-project').attr('disabled', 'disabled');
        }

        $('#des-caminho-xml_aux strong').html($(this).val());
        $('#des-caminho-xml_aux').show();
        $('#des-caminho-xml_file').hide();
        $(this).hide();
    });
    $('#des-caminho-xml_aux .cancel').on('click', function() {
        $('#des-caminho-xml').val('');
        $('#des-caminho-xml_aux strong').html('');
        $('#des-caminho-xml_aux').hide();
        $('#des-caminho-xml_file').show();
        $('#frk-project').removeAttr('disabled');
        return false;
    });

    $('#frk-project').on('change', function() {
        if($(this).val() != '') {
            $('#des-caminho-xml_file').attr('disabled', 'disabled');
        }else{
            $('#des-caminho-xml_file').removeAttr('disabled');
        }
    });
});