$(document).ready(function () {
  closeModal();
  openModal(".edit-btn", "#editBorrower", openEdit);
  openModal(".delete-borrower", "#deleteBorrower", openDelete);
  editForm();
});

function validateForm(formSelector) {
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

  $(formSelector)
    .find("input, select, textarea, checkbox")
    .each(function () {
      $(this).removeClass("error");
      $(this).next("span").remove();

      if (!$(this).val()) {
        $(this)
          .addClass("error")
          .after("<span>" + messages.required + "</span>");
        $(formSelector).addClass("invalid-form");
      }
    });

  return !$(formSelector).hasClass("invalid-form");
}

function editForm() {
  $(".submit-edit").on("click", function (event) {
    event.preventDefault();
    let formValues = $(".edit-form").serialize();

    // if (validateForm(".edit-form"))
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
        console.log(response.responseText);
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
  let b_id = $(thisValue).parents(".jai-data-row").find(".jai-col-ID").html();

  $(modalName).find("#b_id").val(b_id);
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

// function populateEditFields() {
//   $("#editBorrower").on("show.bs.modal", function (e) {
//     console.log("populate data now");
//     let b_id = $(this).data("borrower"),
//       dataset = $(".jai-data-row[data-row-id=" + b_id + "]").find(
//         ".hidden-field > input"
//       ),
//       firstName = dataset.data("jaiFirstname"),
//       middleName = dataset.data("jaiMiddlename"),
//       lastName = dataset.data("jaiLastname"),
//       birthday = dataset.data("jaiBirthday"),
//       contactNo = dataset.data("jaiContactno"),
//       address = dataset.data("jaiAddress"),
//       occupation = dataset.data("jaiOccupation"),
//       businessName = dataset.data("jaiBusinessName"),
//       coMaker = dataset.data("jaiComaker"),
//       coMakerNo = dataset.data("jaiComakerno");

//     console.log(
//       b_id +
//         firstName +
//         middleName +
//         lastName +
//         birthday +
//         contactNo +
//         address +
//         occupation +
//         businessName +
//         coMaker +
//         coMakerNo
//     );
//   });
// }
