document.addEventListener("DOMContentLoaded", init, false);

function init() {
    document.getElementsByName("files[]")[0].addEventListener("change", function(e){
        if(!e.target.files) return;

        selDiv = document.querySelector("#selectedFiles");        
        selDiv.innerHTML = "";
        
        var files = e.target.files;
        for(var i=0; i<files.length; i++) {
            selDiv.innerHTML += files[i].name + "<br/>";
        }
    }); 

    document.getElementById("emailForm").addEventListener("submit", function(e){
        if (!form_validator(this)) e.preventDefault();
    });

    tinymce.init({
      selector: '#emailer_text',
      theme: 'modern',
      height: 300,
      plugins: [
        'advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
        'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
        'save table contextmenu directionality emoticons template paste textcolor'
      ],
      toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons'
    });
}

function form_validator(form) {
    if (form.emailer_subj.value=='' || form.emailer_subj.value=='Тема письма') { 
        alert('Укажите тему письма.'); 
        form.emailer_subj.focus(); 
        return false; 
    }
    if (form.emailer_mails.value=='' || form.emailer_mails.value=='Почтовые адреса(через запятую)') { 
        alert('Укажите адреса получаталей.'); 
        form.emailer_mails.focus(); 
        return false; 
    } else {
        if (!email_validator(form.emailer_mails.value)) {
            alert('Введен некорректный адрес получателя.'); 
            return false;
        }
    }
    if (form.emailer_yourmail.value=='' || form.emailer_yourmail.value=='Ваша почта') { 
        alert('Укажите адрес отправителя.'); 
        form.emailer_yourmail.focus(); 
        return false; 
    } else {
        if (!email_validator(form.emailer_yourmail.value)) {
            alert('Введен некорректный адрес отправителя.'); 
            return false;
        }
    }
    return true;
}

function email_validator(emails){
    var isValid = false;
    emails = emails.split(',');

    for(var i = 0, len = emails.length; i<len; i++){
        var email = emails[i].trim();
        if(email === '') continue;
        isValid = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email);
        if(!isValid) return false;
    }
    return true;
}