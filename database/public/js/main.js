$(document).ready(function () {
  openDelete();
  closeDelete();
});

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

    modalBody.text(constText + modalBorrName.replace("Name:", ""));
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
