jQuery(document).ready(function($){
    let observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            let oldValue = mutation.oldValue;
            let newValue = mutation.target.textContent;
            if (oldValue !== newValue) {
                let qty = parseInt($('.quantity input').val())
                newValue =  $('.woocommerce-variation bdi').html()
                newValue = newValue.split('</span>')[1]
                newValue = parseFloat(newValue)
                newValue = newValue * qty
                $('#my-custom-price').html('$'+newValue.toFixed(2)+'<span style="font-weight: 300"> excl GST</span>')
                if(qty > 1){
                    let price =  $('.woocommerce-variation bdi').html()
                    $("#my-custom-per-item-price").html(`(${price} per Piece)`)
                }
            }
          });
    })
    let checker = new MutationObserver((mutations) => {

        let my_colours = $("#pa_tape-colour option")
        set_colours(my_colours)
    })
    let zipChecker = new MutationObserver((mutations) => {
        console.log("RAWR")
        let my_colours = $("#pa_zip-colour option")
        set_colours(my_colours)
    })
   
    if(document.body.getElementsByClassName("woocommerce-variation")[0]){
        observer.observe(document.body.getElementsByClassName("woocommerce-variation")[0], {
            characterDataOldValue: true, 
            subtree: true, 
            childList: true, 
            characterData: true
        });
    }
    if(document.body.getElementsByTagName('select')[2]){
        console.log("RAWR")
        let my_colours = $("#pa_zip-colour option")
        set_colours(my_colours, "zip")
        
    }else{
        if(document.getElementsByTagName('select')[1]){
            checker.observe(document.body.getElementsByTagName('select')[1],{
                characterDataOldValue: true, 
                subtree: true, 
                childList: true, 
                characterData: true
            })
        }
    }

    $(".quantity input").change(function(){
        if($('#multiples').data('step')){
            $(this).attr('min',$('#multiples').data('step'))
        }
        let html = $('.price bdi').html()
        html = html.split('</span>')[1]
        html = parseFloat(html)
        html = html * parseInt($(this).val())
        if($("#my-custom-price").hasClass('strikethrough')){
            let html2 = $('.price ins bdi').html()
            html2 = html2.split('</span>')[1]
            html2 = parseFloat(html2)
            html2 = html2 * parseInt($(this).val())
            $("#my-custom-price").html('$'+html.toFixed(2))   
            $(".sale-price").html('$'+html2.toFixed(2)+'<span style="font-weight: 300"> excl GST</span>') 
        }else{
            $("#my-custom-price").html('$'+html.toFixed(2)+'<span style="font-weight: 300"> excl GST</span>')
        }
    })

    $(".reset_variations").click(function(){
        $(".colour-picker-item.tape").each(function(){
            $(this).removeClass('selected')
        })
    })


    function set_colours(colours, type="tape"){
        
        $(".colour-picker-item."+type).each(function(){
            $(this).removeClass('hide')
        })
        
        let my_colours = [...colours]
        my_colours = my_colours.map((colour) => {
            return colour.value
        })
        console.log(my_colours)
        $(".colour-picker-item."+type).each(function(){
            if(!my_colours.includes($(this).data('slug'))){
                $(this).addClass('hide')
            }
        })
    }

    
    $(".colour-picker-item.tape").click(function(){
        console.log("Clicked")
        $(".colour-picker-item.tape").each(function(){
            $(this).removeClass('selected')
        })
        $(this).addClass('selected')
        $('#pa_tape-colour').val($(this).data('slug'))
        $('#pa_tape-colour').change()
    })
    $(".colour-picker-item.zip").click(function(){
        console.log("Clicked")
        $(".colour-picker-item.zip").each(function(){
            $(this).removeClass('selected')
        })
        $(this).addClass('selected')
        $('#pa_zip-colour').val($(this).data('slug'))
        $('#pa_zip-colour').change()
    })

    $("#add-to-quote-submit-form").submit(function(e){
        let hidden = $(this).find("input:first-child")
        let quantity = $("input[name='quantity']").val()
        let product_id = $("input[name='product_id'").val() === undefined ? $('.single_add_to_cart_button').val() : $("input[name='product_id'").val()
        let variation_id = $("input[name='variation_id'").val() === undefined ? 0 : $("input[name='variation_id'").val() 

        hidden.attr('name', `quantity-${product_id}-${variation_id}`)
        hidden.val(quantity)

    });
});