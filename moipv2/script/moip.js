(function($) {
    
    $(document).ready( function(){
        $("#phone").mask("(99)9999-9999");
        $("#phone_mobile").mask("(99)9999-99999");
        $('.moipay .pchoice').click(function(){
            $('.moipay .pchoice').removeClass('active-choice');
            $(this).addClass('active-choice');
        });

    });
 
MoipPagamentos = function(){
    
    $('input[name="payment"]').click(function(){
        var form_id = this.value;
        $('.escolha:visible').fadeOut();
        $('#' + form_id).fadeIn();
        $('input:radio[name=payment]').each(function() {
            if ($(this).is(':checked'))
                paymentForm = $(this).attr('id');
        });        
        $("input:hidden[name=paymentMethod]").val(form_id);
        $("input:hidden[name=paymentForm]").val(paymentForm);
        $("input:hidden[name=paymentBank]").val(paymentForm);

        /*------- MASK --------**/

        $("#telefonePortador").mask("(99)9999-9999");
        $("#cpfPortador").mask("999.999.999-99");
        $("#dataPortador").mask("99/99/9999");
        
        if(paymentForm == "AmericanExpress"){
            $("#segurancaNumero").mask("9999");
        }else if(paymentForm == "Diners"){
            $("#cartaoNumero").mask("9999 999999 9999");
            $("#segurancaNumero").mask("999");
        }else if (paymentForm == "Hipercard"){
            
            $("#segurancaNumero").mask("999");
        }else if (paymentForm == "Mastercard"){
            $("#cartaoNumero").mask("9999 9999 9999 9999");
            $("#segurancaNumero").mask("999");
        }else if (paymentForm == "Visa"){
            $("#cartaoNumero").mask("9999 9999 9999 9999");
            $("#segurancaNumero").mask("999");
        }else if (paymentForm == "ELO"){
         
            $("#segurancaNumero").mask("999");
        }else if (paymentForm == "Hipper"){
         
            $("#segurancaNumero").mask("999");
        }else{
          
            $("#segurancaNumero").mask("999?9");
        }
        /*------- [x]MASK --------**/

    });

    $('select[name=parcelamentoCartao]').click(function(){
        parcelamentoCartao = $("select[name=parcelamentoCartao]").find('option').filter(':selected').attr('title');
        $(".parcelamentoCartao").text(parcelamentoCartao);
    });

    /*------- EXECUTE --------**/
    
     
    $('.moip-btn').click(function() {
       
        disableButton();
        paymentMethod = $(this).attr('id');
            
        if(paymentMethod == 'CREDIT_CARD'){
            
            var cc = new Moip.CreditCard({
                        number  : $("#cartaoNumero").val(),
                        cvc     : $("#segurancaNumero").val(),
                        expMonth: $("#cartaoMes").val(),
                        expYear : $("#cartaoAno").val(),
                        pubKey  : $("#id-chave-publica").val()
                    });
             console.log(cc);
            console.log(cc.hash());
            console.log(cc.isValid());
            if(cc.isValid()){
              $("#paymentHASH").val(cc.hash());
            }
            else{
                
                $("#paymentHASH").val('');
            }
            $(".formulario").validate({
                rules : {
                    telefonePortador : {
                        required : true
                    },
                    paymentHASH : {
                        required : true
                    },
                    nomePortador: {
                        required : true
                    },
                    dataPortador: {
                        required : true
                    },
                    cpfPortador: {
                        required : true
                    }
                },
                messages : {
                    paymentHASH: "Dados de cartão inválido, verifique os dados e tente novamente",
                    cartaoNumero: "Informe o número do cartão de crédito corretamente",
                    segurancaNumero: "Preencha o código de segurança",
                    cartaoMes: "Preencha o mês de vencimento do cartão",
                    cartaoAno: "Preencha o ano de vencimento do cartão",
                    telefonePortador : "Preencha o telefone do titular do cartão (<i>Ex. (11)1111-1111</i>)",
                    nomePortador : "Preencha o nome do titular do cartão",
                    dataPortador : "Preencha a data de nascimento do titular do cartão (<i>Ex. 30/11/1980</i>)",
                    cpfPortador : "Preencha o CPF do titular do cartão (<i>Ex. 111.111.111-11</i>)"
                },
                errorClass: "validate_erro",
                errorElement: "li",
                ignore: ".ignore",
                errorLabelContainer: "#alert-area",
                invalidHandler: function(){
                     enableButton();
                },
                submitHandler: function() {
                     $("#"+paymentMethod).submit();
                },
            });

            
        }else if(paymentMethod == 'ONLINE_BANK_DEBIT'){

            
            $(".formulario").validate({
                ignore: ".required",
                submitHandler: function() {
                  $("#"+paymentMethod).submit();
                }
            });

        }else if(paymentMethod == 'BRADESCO'){

            

            $(".formulario").validate({
                ignore: ".required",
                submitHandler: function() {
                   $("#"+paymentMethod).submit();
                }
            });

        }
        
    });

/*------- [x]EXECUTE --------**/
calcParcela = function(){
    var url_mod_dir = $("input:hidden[name=paymentUrl]").val();
    var ammount_calc = $("input:hidden[name=paymentOrderValue]").val();
    
    $.ajax({
              method: "GET",
              url: url_mod_dir+"moipv2/Parcelas.php",
              data: { Method: "cart", price_order: ammount_calc}
            })
            .done(function( data ) {
                    response = $.parseJSON(data);
                   $.each(response, function(key, value){
                        console.log(key + ":" + value)
                        $('#parcelamentoCartao').append('<option value="' + key + '" label="' + key + ' x ' + value + '" title="Parcelado em ' + key + ' x ' + value + '" class="pagamentoParcelado">' + key + ' x ' + value + '</option>');
                    })
                
            });
    console.log(url_mod_dir +' calc'+ ammount_calc);
}

disableButton = function(){

    $(".moip-btn").each(function( index ) {
        $(".moip-btn").fadeOut();
    });
     $(".spinner_moip").each(function( index ) {
        $(".spinner_moip").fadeIn();
    });
    
}

enableButton = function(){
     $(".moip-btn").each(function( index ) {
        $(".moip-btn").fadeIn();
    });
      $(".spinner_moip").each(function( index ) {
        $(".spinner_moip").fadeOut();
    });
    
}



}


})(jQuery);