// Wait for the DOM to be ready
$(document).ready(function(){
    getListUsers();
    $('#btn_ajouter').click(function () {
      //chargé le form dans content
      $("#content").load("View/pages/create_user.php", function(){
          validateForm();
      });
  });
    $('#btn_modifier').click(function (event){
      console.log($("#userselected").val()); //Affiche maDiv
    })
    $('#btn_supprimer').click(function (event) {
      delete_user($("#userselected").val());
    })
  
});


function getListUsers(name) {
  $.ajax({
    url: "index.php", // on donne l'URL du fichier de traitement
    type: "GET", // la requête est de type POST
    data: "controller=user&action=userList&name=" + name, // et on envoie nos données,
    datatype: "json",
    success: function (data) {
      createTable(data);
    }
  });
}

function validateForm() {
  $("#registration").validate({
    rules: {
      "LastName": {
        "required": true,
        "minlength": 2,
        "maxlength": 60000
      },
      "FirstName": {
        "required": true,
        "minlength": 2,
        "maxlength": 60000
      },
      "Email": {
        "required": true,
        "maxlength": 255
      },
      "Phone": {
        "required": true
      },
      "Password": {
        required: true,
        minlength: 8
      },
      messages: {
        LastName: {
          required: "Please enter a valid LastName",
        },
        FirstName: {
          required: "Please enter your FirstName",
        },
        Password: {
          required: "Please provide a password",
          minlength: "Your password must be at least 8 characters long"
        },
        Email: {
          required: "Please enter a valid email address"
        }
      },

    }
  })
}

function createTable(data) {
  $('#content').empty();
  $('#content').append('<table id="fieldsetTabUser"></table>');
  var row = JSON.parse(data);
  var header = {
    "name_user": "Nom",
    "surname_user": "Prenom",
    "role_user": "Role",
    "email": "@mail",
    "phone": "Phone",
    "id_flat": "Id_flat",
  };
  createRow(header);
  $.each(row, function (index) {
    createRow(row[index]);
  });
  $('#fieldsetTabUser tr').click(function () {
   var id =  $(this).attr('id');
   $('#userselected').val(id);
  });
}
function createRow(data) {
  var row = $('<tr id="' + data.id_user + '" />')
  $('#fieldsetTabUser').append(row);
  row.append($("<td>" + data.name_user + "</td>"));
  row.append($("<td>" + data.surname_user+ "</td>"));
  row.append($("<td>" + data.role_user + "</td>"));
  row.append($("<td>" + data.email + "</td>"));
  row.append($("<td>" + data.phone + "</td>"));
  row.append($("<td>" + data.id_flat+ "</td>"));
  
}

function delete_user(id) {
  $.ajax({
    url: "index.php", // on donne l'URL du fichier de traitement
    type: "GET", // la requête est de type POST
    data: "controller=user&action=delete&id_user=" + id, // et on envoie nos données,
    datatype: "json",
    success: function(data) {
       getListUsers();
    }

  });
}
