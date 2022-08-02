$(document).ready(function () {
  closeModal();
  openModal(".edit-btn", "#editBorrower", openEdit);
  openModal(".delete-borrower", "#deleteBorrower", openDelete);
  openModal(".create-borrower", "#createBorrower", false);
  openModal(".btn-new-loan", "#createloan", false);
  editForm();
  inputMask();
  createDatepicker();
  validateInputs();
  createForm();
});

/*                                */
/*         Global Variables       */
/*                                */

const validationMessages = {
  name: "Please enter a valid name",
  firstname: "Please enter a valid first name",
  middlename: "Please enter a valid middle name",
  lastname: "Please enter a valid last name",
  date: "Please enter a valid date",
  phone: "Please enter a valid phone number",
  occupation: "Please enter a occupation",
  required: "This field is required",
};

/*                                */
/*    Modal Related Functions     */
/*                                */

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
  $(".close-modal, .close-container").on("click", function (event) {
    event.preventDefault();
    let modalName = "#" + $(this).parents(".modal.fade").attr("id");
    $(modalName).modal("toggle");
  });

  $(".modal").on("hidden.bs.modal", function () {
    if ($(".modal-content:hidden")) {
      $(".modal-content").show();
      $(".success-message").hide();
    }
  });
}

/*                                */
/*    Form Related Functions      */
/*                                */

function editForm() {
  let inputChanged = false;

  $(".submit-edit").on("click", function (event) {
    event.preventDefault();
    let formValues = $(".edit-form").serialize(),
      newValues = $(".edit-form").serializeArray();
    rowId = $(this)
      .parents(".modal-content")
      .find('input[name="data-row"]')
      .val();

    if (validateForm(".edit-form"))
      $.ajax({
        url: "../ajax-calls/edit-borrower.php",
        method: "POST",
        data: formValues,
        dataType: "json",
        beforeSend: function () {},
        success: function (data) {
          let borrower =
              data.firstname + " " + data.middlename + " " + data.lastname,
            contactno = data.contactno,
            address = data.address,
            comaker = data.comaker,
            comakerno = data.comakerno;
          console.log(rowId);

          $(".jai-data-row").each(function () {
            if ($(this).data("row") == rowId) {
              $(this).find(".jai-table-name .value").text(borrower);
              $(this).find(".jai-table-contact .value").text(contactno);
              $(this).find(".jai-table-address .value").text(address);
              $(this).find(".jai-table-comaker .value").text(comaker);
              $(this).find(".jai-table-comakerno .value").text(comakerno);

              $(this)
                .find(".hidden-form input")
                .each(function () {
                  let inputName = $(this).attr("name"),
                    input = $(this);

                  $(newValues).each(function () {
                    if (inputName == this["name"]) {
                      input.val(this["value"]);
                    }
                  });
                });
            }
          });

          $("#editBorrower .modal-content").fadeOut(300, function (param) {
            $(".success-message").fadeIn(300, function () {
              setTimeout(function () {
                if ($("body").hasClass("modal-open"))
                  $("#editBorrower").modal("hide");
              }, 2000);
            });
          });
        },
        error: function (response) {
          console.log("error");
          console.log(response);
        },
      });
  });
}

function createForm() {
  let inputChanged = false;

  $(".submit-create").on("click", function (event) {
    event.preventDefault();
    let formValues = $(".create-form").serialize();

    if (validateForm(".create-form"))
      $.ajax({
        url: "../ajax-calls/create-borrower.php",
        method: "POST",
        data: formValues,
        dataType: "json",
        beforeSend: function () {},
        success: function (data) {
          console.log(data);

          $("#createBorrower .modal-content").fadeOut(300, function (param) {
            $(".success-message").fadeIn(300, function () {
              setTimeout(function () {
                if ($("body").hasClass("modal-open"))
                  $("#createBorrower").modal("hide");
              }, 2000);
            });
          });
        },
        error: function (response) {
          console.log("error");
          console.log(response);
        },
      });
  });
}

function createDatepicker() {
  let date = new Date(),
    maxDate = 0,
    maxYear = "1940:c+nn";
  $(".datepicker").each(function () {
    maxDate = $(this).hasClass("no-limit") ? null : 0;
    maxYear = $(this).hasClass("no-limit") ? '1940:c+10' : '1940:c+nn';

    $(this).datepicker({
      dateFormat: "yy-mm-dd",
      minDate: "1940-01-01",
      setDate: date,
      changeMonth: true,
      changeYear: true,
      maxDate: maxDate,
      yearRange: maxYear,
    });
  });

  $(".datepicker").on("click contextmenu", function () {
    if ($(".select-selected").length < 2) {
      customSelectMonth();
      customSelectYear();
    }
  });
  $(document).on(
    "click contextmenu",
    ".select-items > div, .ui-datepicker-next, .ui-datepicker-prev",
    function () {
      customSelectMonth();
      customSelectYear();
    }
  );
}

function customSelectMonth() {
  if ($("ui-datepicker-title")) {
    var x, i, j, l, ll, selElmnt, a, b, c;
    x = document.getElementsByClassName("ui-datepicker-title");
    l = x.length;
    for (i = 0; i < l; i++) {
      selElmnt = x[i].getElementsByTagName("select")[0];
      ll = selElmnt.length;
      a = document.createElement("DIV");
      a.setAttribute("class", "select-selected");
      a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
      x[i].appendChild(a);
      b = document.createElement("DIV");
      b.setAttribute("class", "select-items select-hide");
      for (j = 0; j < ll; j++) {
        c = document.createElement("DIV");
        c.innerHTML = selElmnt.options[j].innerHTML;
        c.addEventListener("click", function (e) {
          var y, i, k, s, h, sl, yl;
          s = this.parentNode.parentNode.getElementsByTagName("select")[0];
          sl = s.length;
          h = this.parentNode.previousSibling;
          for (i = 0; i < sl; i++) {
            if (s.options[i].innerHTML == this.innerHTML) {
              s.selectedIndex = i;
              h.innerHTML = this.innerHTML;
              y = this.parentNode.getElementsByClassName("same-as-selected");
              yl = y.length;
              for (k = 0; k < yl; k++) {
                y[k].removeAttribute("class");
              }
              this.setAttribute("class", "same-as-selected");
              $(".ui-datepicker-month").val(i).trigger("change");
              break;
            }
          }
          h.click();
        });
        b.appendChild(c);
      }
      x[i].appendChild(b);
      a.addEventListener("click", function (e) {
        e.stopPropagation();
        closeAllSelect(this);
        this.nextSibling.classList.toggle("select-hide");
        this.classList.toggle("select-arrow-active");
      });
    }
    function closeAllSelect(elmnt) {
      var x,
        y,
        i,
        xl,
        yl,
        arrNo = [];
      x = document.getElementsByClassName("select-items");
      y = document.getElementsByClassName("select-selected");
      xl = x.length;
      yl = y.length;
      for (i = 0; i < yl; i++) {
        if (elmnt == y[i]) {
          arrNo.push(i);
        } else {
          y[i].classList.remove("select-arrow-active");
        }
      }
      for (i = 0; i < xl; i++) {
        if (arrNo.indexOf(i)) {
          x[i].classList.add("select-hide");
        }
      }
    }
    document.addEventListener("click", closeAllSelect);
  }
}

function customSelectYear() {
  if ($("ui-datepicker-title")) {
    var x, i, j, l, ll, selElmnt, a, b, c;
    x = document.getElementsByClassName("ui-datepicker-title");
    l = x.length;
    for (i = 0; i < l; i++) {
      selElmnt = x[i].getElementsByTagName("select")[1];
      ll = selElmnt.length;
      a = document.createElement("DIV");
      a.setAttribute("class", "select-selected");
      a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
      x[i].appendChild(a);
      b = document.createElement("DIV");
      b.setAttribute("class", "select-items select-hide");
      for (j = 0; j < ll; j++) {
        c = document.createElement("DIV");
        c.innerHTML = selElmnt.options[j].innerHTML;
        c.addEventListener("click", function (e) {
          var y, i, k, s, h, sl, yl;
          s = this.parentNode.parentNode.getElementsByTagName("select")[1];
          sl = s.length;
          h = this.parentNode.previousSibling;
          for (i = 0; i < sl; i++) {
            if (s.options[i].innerHTML == this.innerHTML) {
              s.selectedIndex = i;
              h.innerHTML = this.innerHTML;
              y = this.parentNode.getElementsByClassName("same-as-selected");
              yl = y.length;
              for (k = 0; k < yl; k++) {
                y[k].removeAttribute("class");
              }
              this.setAttribute("class", "same-as-selected");
              $(".ui-datepicker-year").val(this.innerHTML).trigger("change");
              break;
            }
          }
          h.click();
        });
        b.appendChild(c);
      }
      x[i].appendChild(b);
      a.addEventListener("click", function (e) {
        e.stopPropagation();
        closeAllSelect(this);
        this.nextSibling.classList.toggle("select-hide");
        this.classList.toggle("select-arrow-active");
      });
    }
    function closeAllSelect(elmnt) {
      var x,
        y,
        i,
        xl,
        yl,
        arrNo = [];
      x = document.getElementsByClassName("select-items");
      y = document.getElementsByClassName("select-selected");
      xl = x.length;
      yl = y.length;
      for (i = 0; i < yl; i++) {
        if (elmnt == y[i]) {
          arrNo.push(i);
        } else {
          y[i].classList.remove("select-arrow-active");
        }
      }
      for (i = 0; i < xl; i++) {
        if (arrNo.indexOf(i)) {
          x[i].classList.add("select-hide");
        }
      }
    }
    document.addEventListener("click", closeAllSelect);
  }
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

  $(".alphanumeric").mask("X", {
    translation: {
      X: {
        pattern: /[a-zA-Z0-9 ]/,
        recursive: true,
      },
    },
  });

  $(".money").mask("000,000,000,000");
}

function validateForm(form) {
  $(form)
    .find(
      "input[required]:not([type='hidden']), select[required]:not([type='hidden']), textarea[required]:not([type='hidden']), checkbox[required]:not([type='hidden'])"
    )
    .each(function () {
      clearErrors(this);
      checkEmptyInput(this);
    });

  return !$(form).find(".error").length;
}

function validateInputs() {
  $(
    "input[required]:not([type='hidden']), select[required]:not([type='hidden']), textarea[required]:not([type='hidden']), checkbox[required]:not([type='hidden'])"
  ).on("input", function (event) {
    clearErrors(this);
    checkEmptyInput(this);
  });
}

function checkEmptyInput(thisInput) {
  if (!$(thisInput).val()) {
    $(thisInput)
      .addClass("error")
      .after("<span>" + validationMessages.required + "</span>");
  }
}

function clearErrors(thisInput) {
  $(thisInput).removeClass("error");
  $(thisInput).next("span").remove();
}
