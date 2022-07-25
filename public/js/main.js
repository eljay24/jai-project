$(document).ready(function () {
  closeModal();
  openModal(".edit-btn", "#editBorrower", openEdit);
  openModal(".delete-borrower", "#deleteBorrower", openDelete);
  editForm();
  inputMask();
});

function validateForm(form) {
  let messages = {
    name: "Please enter a valid name",
    firstname: "Please enter a valid first name",
    middlename: "Please enter a valid middle name",
    lastname: "Please enter a valid last name",
    date: "Please enter a valid date",
    phone: "Please enter a valid phone number",
    occupation: "Please enter a occupation",
    required: "This field is required",
  };

  $(form)
    .find(
      "input[required]:not([type='hidden']), select[required], textarea[required], checkbox[required]"
    )
    .each(function () {
      $(this).removeClass("error");
      $(this).next("span").remove();

      if (!$(this).val()) {
        $(this)
          .addClass("error")
          .after("<span>" + messages.required + "</span>");
        console.log($(this));
      }
    });

  return !$(form).find(".error").length;
}

function editForm() {
  $(".submit-edit").on("click", function (event) {
    event.preventDefault();
    let formValues = $(".edit-form").serialize();

    if (validateForm(".edit-form"))
      $.ajax({
        url: "../ajax-calls/edit-borrower.php",
        method: "POST",
        data: formValues,
        dataType: "json",
        beforeSend: function () {},
        success: function (data) {
          console.log(data);

          // $(".row").each(function () {
          //   if ($(this).attr("data-row-id") == b_id) {
          //     $(this)
          //       .find(".jai-table-name")
          //       .text("Name: " + data);
          //   }
          // });
          //
          $("#editBorrower").modal("toggle");
        },
        error: function (response) {
          console.log("error");
          console.log(response);
        },
      });
  });
}

function openModal(buttonName, modalName, modalFunction) {
  $(buttonName).on("click", function (event) {
    event.preventDefault();
    if (modalFunction) {
      modalFunction(buttonName, modalName, $(this));
    }
    $(modalName).modal("toggle");
  });
}

function openEdit(buttonName, modalName, thisValue) {
  let formValues = $(thisValue)
      .parent()
      .siblings(".hidden-field")
      .find(".hidden-form")
      .serializeArray(),
    modalInput = $(modalName).find("form.edit-form input");

  modalInput.each(function () {
    let inputName = $(this).attr("name"),
      input = $(this);

    $(formValues).each(function () {
      if (inputName == this["name"]) {
        input.val(this["value"]);
      }
    });
  });
}

function openDelete(buttonName, modalName, thisValue) {
  let modalParent = $("#deleteBorrower"),
    constText = "Are you sure you want to delete ",
    modalBody = modalParent.find(".modal-body"),
    modalID = thisValue.parents(".jai-data-row").find(".jai-col-ID").text(),
    deleteID = modalParent.find(".delete-form input"),
    modalBorrName = thisValue
      .parents(".jai-data-row")
      .find(".jai-table-name")
      .text();

  modalBody.text(constText + modalBorrName.replace("Name:", "") + "?");
  deleteID.attr("value", modalID);
  modalParent.modal("toggle");
}

function closeModal() {
  $(".close-modal").on("click", function (event) {
    event.preventDefault();
    let modalName = "#" + $(this).parents(".modal.fade").attr("id");
    $(modalName).modal("toggle");
  });
}

function inputMask() {
  $(".phone-number").mask("+63 000-000-0000");

  $(".letters-only").mask("Z", {
    translation: {
      Z: {
        pattern: /[a-zA-Z ]/,
        recursive: true,
      },
    },
  });

  $(".alphanumeric']").mask("X", {
    translation: {
      X: {
        pattern: /[a-zA-Z0-9 ]/,
        recursive: true,
      },
    },
  });
}
