$(document).ready(function () {
  closeModal();
  openModal(".edit-btn", "#editBorrower", openEdit);
  openModal(".delete-borrower", "#deleteBorrower", openDelete);
  editForm();
  populateEditFields();
});

function editForm() {
  $(".submit-edit").on("click", function (event) {
    event.preventDefault();
    let fname = $('input[name="firstname"').val(),
      mname = $('input[name="middlename"').val(),
      lname = $('input[name="lastname"').val(),
      b_id = $(this).parents("#editBorrower").attr("data-borrower");

    $.post(
      "../ajax-calls/edit-borrower.php",
      {
        firstname: fname,
        middlename: mname,
        lastname: lname,
        bid: b_id,
      },
      function (data, status) {
        $(".row").each(function () {
          if ($(this).attr("data-row-id") == b_id) {
            $(this)
              .find(".jai-table-name")
              .text("Name: " + data);
          }
        });
      }
    );
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

  $(modalName).attr("data-borrower", b_id);
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

function populateEditFields() {
  $("#editBorrower").on("show.bs.modal", function (e) {
    console.log("populate data now");
    let b_id = $(this).data("borrower");

    console.log($(".jai-data-row[data-row-id=" + b_id + "]").find(".hidden-field > input"));
  });
}
