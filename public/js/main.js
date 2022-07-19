$(document).ready(function () {
  openDelete();
  closeDelete();
  openModal(".edit-btn", "#editBorrower", console.log("function working"));
  editForm();
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
        console.log(data);
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
  $(buttonName).on("click", function () {
    modalFunction;
    let b_id = $(this).parents(".jai-data-row").find(".jai-col-ID").html();

    $(modalName).attr("data-borrower", b_id);
    $(modalName).modal("toggle");
  });
}

function openDelete() {
  $(".delete-borrower").on("click", function () {
    let modalParent = $("#deleteBorrower"),
      constText = "Are you sure you want to delete ",
      modalBody = modalParent.find(".modal-body"),
      modalID = $(this).parents(".jai-data-row").find(".jai-col-ID").text(),
      deleteID = modalParent.find(".delete-form input"),
      modalBorrName = $(this)
        .parents(".jai-data-row")
        .find(".jai-table-name")
        .text();

    modalBody.text(constText + modalBorrName.replace("Name:", "") + "?");
    deleteID.attr("value", modalID);
    modalParent.modal("toggle");
  });
}

function closeDelete() {
  $(".close-modal").on("click", function (event) {
    event.preventDefault();
    let modalName = "#" + $(this).parents(".modal.fade").attr("id");
    $(modalName).modal("toggle");
  });
}
