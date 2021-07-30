jQuery(document).ready(function($){
    let length = 0
    let chars = 0
    let nums = 0
    let score = 0
    const LETTERS = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','C','B','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z']
    const NUMS = ['1','2','3','4','5','6','7','8','9','0']
    const SYMS = ['!','@','#','$','%','^','&','*','(',')','+','_','-',':',';','<','>','?']
    $("#input-password").on('input',function(){
        const pass = $(this).val()
        score = 0
        length = pass.length
        LETTERS.forEach(letter => {
            if(pass.includes(letter)){
                score += 8
            }
        })
        NUMS.forEach(num => {
            if(pass.includes(num)){
                score += 5
            }
        })
        SYMS.forEach(sym => {
            if(pass.includes(sym)){
                score += 15
            }
        })

        console.log(score)
        $("#score-bar .inner").css("width", `${score/100*100}%`)
    })
})