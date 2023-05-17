$(document).ready(function () {
  // Table Actions
  if ($(".borrower-table").length) {
    searchTable("refresh-borrowers.php");
    paginateTable("refresh-borrowers.php");
  }
  if ($(".payments-table").length) {
    searchTable("refresh-payments.php");
    paginateTable("refresh-payments.php");
  }

  // Form Modifications
  inputMask();
  createDatepicker();
  validateInputs();
  setToZero();
  imgInput();
  fakeSelectEvents();
  triggerSubmit();

  // Autofill Functions
  autofillChoiceActions();
  autoFillAction("#namesearch", "suggestions.php");
  autoFillAction("#newloansearch", "suggestions-new-loan.php");

  // Toggle Modal Functions START
  openModal(
    ".create-borrower",
    ".form-modal",
    "submit-create",
    resetForm,
    "input[name='firstname']"
  );
  openModal(".edit-btn", ".form-modal", "submit-edit", openEdit);
  openModal(".delete-borrower", "#deleteBorrower", "", openDelete);

  openModal(
    ".btn-new-loan",
    ".form-modal",
    "submit-loan",
    resetForm,
    ".autocomplete-input"
  );

  openModal(
    ".open-payment-modal",
    "#paymentModal",
    "submit-payment",
    resetForm,
    ".autocomplete-input"
  );

  closeModal();
  // Toggle Modal Functions END

  // Modal Submit Functions START
  submitForm(
    ".submit-create",
    "create-borrower.php",
    messages.successMessages.borrower.create,
    "refresh-borrowers.php"
  );
  submitForm(
    ".submit-edit",
    "edit-borrower.php",
    messages.successMessages.borrower.update,
    "refresh-borrowers.php"
  );

  submitForm(
    ".submit-loan",
    "create-loan.php",
    messages.successMessages.Loan.create
  );

  submitForm(
    ".submit-payment",
    "add-payment.php",
    messages.successMessages.Payment.create,
    "refresh-payments.php"
  );

  submitForm(
    ".add-new",
    "add-payment.php",
    messages.successMessages.Payment.create,
    "refresh-payments.php",
    true
  );

  addPasses();

  // submitForm(
  //   ".add-passes",
  //   "add-passes.php",
  //   messages.successMessages.Payment.create,
  //   "refresh-payments.php",
  // );
  // Modal Submit Functions END
});

/*                                */
/*         Global Variables       */
/*                                */

const messages = {
  validationMessages: {
    name: "Please enter a valid name",
    firstname: "Please enter a valid first name",
    middlename: "Please enter a valid middle name",
    lastname: "Please enter a valid last name",
    date: "Please enter a valid date",
    phone: "Please enter a valid phone number",
    occupation: "Please enter a occupation",
    required: "This field is required",
    noresults: "No results found",
  },
  successMessages: {
    borrower: {
      create: "New Borrower successfully created",
      update: "Borrower successfully updated",
      delete: "Borrower has been deleted",
    },
    Loan: {
      create: "New loan successfully created",
      update: "Loan successfully updated",
    },
    Payment: {
      create: "New payment added",
      update: "Loan successfully updated",
      pass: "Passes added successfully",
    },
  },
  confirmMessages: {
    borrower: {
      delete: "Are you sure you want to delete",
    },
  },
};

/*                                */
/*    Table Related Functions     */
/*                                */

function searchTable(ajaxAction) {
  $(document).on("submit", ".table-search", function (event) {
    event.preventDefault();

    let searchValue = $(this).find(".search-input").val();

    refreshTable(ajaxAction, searchValue, 1);
  });
}

function paginateTable(ajaxAction) {
  $(document).on("click", ".page-link", function (event) {
    event.preventDefault();

    let paginateValue = $(this).data("pagecount");

    refreshTable(ajaxAction, false, paginateValue);
  });
}

function refreshTable(actionFIle, search = false, page = false) {
  if (search == false) {
    search = $(".search-input").val();
  }

  if (page == false) {
    page = $(".page-link.active").data("pagecount");
  }

  let table = $(".jai-table.table-container"),
    pagination = $(".pagination-container");

  $.ajax({
    url: "../ajax-calls/" + actionFIle,
    method: "POST",
    data: {
      action: "get-table",
      search_value: search,
      page_number: page,
    },
    dataType: "json",
    beforeSend: function () {},
    success: function (data, xhr, success) {
      // console.log(data);
      // console.log(xhr);
      // console.log(success);

      table.html(data.table);
      pagination.html(data.pagination);
    },
    error: function (response, xhr, data) {
      // console.log(response);
      // console.log(xhr);
      // console.log(data);
    },
  });
}

/*                                */
/*    Modal Related Functions     */
/*                                */

function openModal(
  buttonName,
  modalName,
  submitBtnClass,
  modalFunction = false,
  focusOn
) {
  $(document).on("click", buttonName, function (event) {
    event.preventDefault();

    clearFormErrors(modalName);

    $(modalName).find(".btn-action:not(.add-passes)").addClass(submitBtnClass);
    if (modalFunction) {
      modalFunction(buttonName, modalName, $(this));
    }
    $(modalName).modal("toggle");
    $(modalName).on("shown.bs.modal", function () {
      $(focusOn).focus();
    });
  });
}

function openEdit(modalClass, modalName, thisValue) {
  let formValues = $(thisValue)
      .parent()
      .siblings(".hidden-field")
      .find(".hidden-form")
      .serializeArray(),
    modalInput = $(modalName).find("form.action-form input");

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
    constText = messages.confirmMessages.borrower.delete,
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

  $(".modal").on("hide.bs.modal", function () {
    $(".btn-action").attr("class", "btn btn-primary btn-sm btn-action");
  });
  $(".modal").on("hidden.bs.modal", function () {
    if ($("#payment").length) $("#payment").prop("readonly", false);
    if ($(".modal-content:hidden")) {
      $(".modal-content").show();
      $(".success-message").hide();
    }
  });
}

/*                                */
/*    Form Related Functions      */
/*                                */

function setToZero() {
  if ($("#type").length) {
    var selectBox = $("#type");
    var payment = $("#payment");
    var selectedValue = selectBox.val();

    clearErrors(payment);
    if (selectedValue == "Pass") {
      payment.val(0);
      payment.prop("readonly", true);
    } else {
      var paymentAmount = payment.val();
      if (paymentAmount != 0) {
        payment.val(paymentAmount);
      } else {
        payment.val("");
      }
      payment.prop("readonly", false);
    }
  }
}

function autoFillAction(input, actionFile) {
  $(input).on("input focus", function () {
    $(this).siblings(".suggestions-container").addClass("show-results");
    var name = $(this).val(),
      thisInput = $(this);
    $.ajax({
      url: "../ajax-calls/" + actionFile,
      method: "POST",
      data: {
        suggestion: name,
      },
      dataType: "json",
      beforeSend: function () {},
      success: function (data, xhr, success) {
        thisInput.siblings(".suggestions-container").empty();
        thisInput.siblings(".suggestions-container").html(data);
        // console.log(data);
        // console.log(xhr);
        // console.log(success);
      },
      error: function (response, xhr, data) {
        // console.log(xhr);
        thisInput
          .siblings(messages.confirmMessages.borrower.noresults)
          .html(data);
      },
    });
  });
}

function autofillChoiceActions() {
  $(document).on("click", function (e) {
    let target = $(e.target);
    if (!target.is(".autocomplete-input")) {
      $(".suggestions-container").removeClass("show-results");
    }
  });

  $(document).on("click", ".suggestion-container", function () {
    suggestionContainerAction($(this));
  });
}

function suggestionContainerAction(thisValue) {
  let datePickerSelector, setMinDate;

  thisValue.parent().siblings(".autocomplete-input").val(thisValue.text());
  thisValue.parent().siblings(".borrower-id").val(thisValue.data("borrower"));
  fillInputs(thisValue.data("borrower"));
  clearErrors(thisValue.parent().siblings(".autocomplete-input"));
  checkEmptyInput(thisValue.parent().siblings(".autocomplete-input"));
  if (thisValue.parents(".action-form").find(".set-min-date").length) {
    datePickerSelector = thisValue
      .parents(".action-form")
      .find(".set-min-date");
    setMinDate = thisValue.data("releasedate");
    datePickerSelector.datepicker("option", "minDate", new Date(setMinDate));
  }
  $("#payment").focus();
}

function fillInputs(id) {
  $.ajax({
    url: "../ajax-calls/get-borrower.php",
    method: "POST",
    data: {
      action: "get-borrower",
      b_id: id,
    },
    dataType: "json",
    success: function (borrowerDetails, status, success) {
      if ($(".payment-form").length) {
        $("#loanamount").val(borrowerDetails[0]["amount"]);
        $("#payable").val(borrowerDetails[0]["payable"]);
        $("#remainingbalance").val(borrowerDetails[0]["balance"]);
        $("#amortization").val(borrowerDetails[0]["amortization"]);
        $("#mode").val(borrowerDetails[0]["mode"]);
        $("#term").val(borrowerDetails[0]["term"]);
        $("#collectorid").val(borrowerDetails[0]["c_id"]);
        $("#collectorname").val(borrowerDetails[0]["c_id"]);
        $("#loanid").val(borrowerDetails[0]["l_id"]);
        $("#name").val(
          borrowerDetails[0]["cfname"] + " " + borrowerDetails[0]["clname"]
        );
        $("#type").val("");
        $("#payment").val("");
        $("#payment").placeholder = parseFloat(
          borrowerDetails[0]["amortization"]
        ).toFixed(2);
      }
    },
    error: function (xghr, status, error) {
      // console.log(xghr);
      // console.log(status);
      // console.log(error);
    },
  });
}

function resetForm() {
  if ($(".action-form").length) {
    let noResetArr = [],
      count = 0;
    $(".action-form .no-reset").each(function () {
      noResetArr.push($(this).val());
    });
    $(".remove-readonly").each(function () {
      $(this).removeAttr("readonly");
    });
    $(".action-form")[0].reset();
    $(".action-form .no-reset").each(function () {
      $(this).val(noResetArr[count]);
      count++;
    });
  }
}

function triggerSubmit() {
  $(".modal input, .modal select, .modal stextarea").on(
    "keydown",
    function (event) {
      let e = event.key;
      if (event.ctrlKey && e == "Enter") {
        if ($(".add-new").length) {
          $(".add-new").click();
        } else if ($(".btn-action:not(.add-passes)").length) {
          $(".btn-action:not(.add-passes)").click();
        }
      }
    }
  );
}

function submitForm(
  submitBtn,
  ajaxFile,
  successMessage,
  tableAction = false,
  addNew = false
) {
  $(document).on("click", submitBtn, function (event) {
    event.preventDefault();
    let form = $(".action-form"),
      formValues = form.serialize();

    if (validateForm(form))
      $.ajax({
        url: "../ajax-calls/" + ajaxFile,
        method: "POST",
        data: formValues,
        dataType: "json",
        beforeSend: function () {
          $(submitBtn).addClass("disabled");
        },
        success: function (response, xhr, data) {
          // console.log(response);
          // console.log(xhr);
          // console.log(data);
          if (tableAction) {
            refreshTable(tableAction);
          }
          $(".success-message .success-content").text(successMessage);

          if (addNew) {
            $("input, textarea, select").blur();
            resetForm();
            clearErrors(form);
            $(".to-focus").focus();
            $(submitBtn).removeClass("disabled");
          } else {
            $(".form-modal .modal-content").fadeOut(150, function () {
              $(".success-message").fadeIn(150, function () {
                $(submitBtn).removeClass("disabled");
                setTimeout(function () {
                  if ($("body").hasClass("modal-open"))
                    $(".form-modal").modal("hide");
                }, 2000);
              });
            });
          }
        },
        error: function (response, xhr, data) {
          // console.log("error");
          // console.log(response);
          // console.log(xhr);
          // console.log(data);
        },
      });
  });
}

function addPasses() {
  let btn = ".add-passes";
  $(document).on("click", btn, function (event) {
    $.ajax({
      url: "../ajax-calls/add-passes.php",
      method: "POST",
      // data: {},
      dataType: "json",
      beforeSend: function () {
        console.log("before send working");
        $(btn).addClass("disabled");
      },
      success: function (response, xhr, data) {
        // console.log("success");
        refreshTable("refresh-payments.php");
        $(".success-message .success-content").text(
          messages.successMessages.Payment.pass
        );

        console.log(response)
        console.log(xhr)
        $(".success-message .daily-pass").text(response.daily);
        $(".success-message .weekly-pass").text(response.weekly);

        // console.log("before fade");
        $(".form-modal .modal-content").fadeOut(150, function () {
          $(".success-message").fadeIn(150, function () {
            $(btn).removeClass("disabled");
            // setTimeout(function () {
            //   if ($("body").hasClass("modal-open"))
            //     $(".form-modal").modal("hide");
            // }, 2000);
          });
        });
      },
      error: function (response, xhr, data) {
        // console.log("error");
        // console.log(response);
        // console.log(xhr);
        // console.log(data);
      },
    });
  });
}

/*                             */
/*      START Validations      */
/*                             */

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
  ).on("input change", function (event) {
    clearErrors(this);
    checkEmptyInput(this);
  });

  $(
    "input[required]:not([type='hidden']):not(.datepicker):not(.autocomplete-input), select[required]:not([type='hidden']), textarea[required]:not([type='hidden']), checkbox[required]:not([type='hidden'])"
  ).on("blur", function (event) {
    clearErrors(this);
    checkEmptyInput(this);
  });
}

function checkEmptyInput(thisInput) {
  if (!$(thisInput).val()) {
    $(thisInput)
      .addClass("error")
      .after(
        "<span class='error-message'>" +
          messages.validationMessages.required +
          "</span>"
      );
  }
}

function clearErrors(thisInput) {
  $(thisInput).removeClass("error");
  $(thisInput).siblings(".error-message").remove();
}

function clearFormErrors(thisForm) {
  $(thisForm)
    .find(
      "input[required]:not([type='hidden']), select[required]:not([type='hidden']), textarea[required]:not([type='hidden']), checkbox[required]:not([type='hidden'])"
    )
    .removeClass("error")
    .siblings(".error-message")
    .remove();
}

/*                           */
/*      END Validations      */
/*                           */

/*                                */
/*      START Modify Forms        */
/*                                */

function imgInput() {
  $(document).on("click", ".form-image", function () {
    $(this).siblings(".img-input").click();
  });

  $(document).on("change", ".img-input", function () {
    const [file] = imgInp.files;
    if (file) {
      formImg.src = URL.createObjectURL(file);
    }
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

function createDatepicker() {
  let date = new Date(),
    maxDate = 0,
    maxYear = "1940:c+nn",
    currentYear = date.getFullYear(),
    currentMonth = date.getMonth() + 1,
    currentDate = date.getDate();

  $(".datepicker").each(function () {
    maxDate = $(this).hasClass("no-limit") ? null : 0;
    maxYear = $(this).hasClass("no-limit") ? "1940:c+10" : "1940:c+nn";

    $(this).datepicker({
      dateFormat: "yy-mm-dd",
      minDate: "1940-01-01",
      setDate: date,
      changeMonth: true,
      changeYear: true,
      maxDate: maxDate,
      yearRange: maxYear,
    });

    if ($(this).hasClass("today"))
      $(this).val(currentYear + "-" + currentMonth + "-" + currentDate);

    if ($(this).hasClass("min-date-today"))
      $(this).datepicker(
        "option",
        "minDate",
        new Date(currentYear + "-" + currentMonth + "-" + currentDate)
      );
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

function createCustomSelect() {
  $("select").each(function () {
    var $this = $(this),
      numberOfOptions = $(this).children("option").length;

    $this.addClass("select-hidden");
    $this.wrap('<div class="select"></div>');
    $this.after('<div class="select-styled"></div>');

    var $styledSelect = $this.next("div.select-styled");
    $styledSelect.text($this.children("option").eq(0).text());

    var $list = $("<ul />", {
      class: "select-options",
    }).insertAfter($styledSelect);

    for (var i = 0; i < numberOfOptions; i++) {
      $("<li />", {
        text: $this.children("option").eq(i).text(),
        rel: $this.children("option").eq(i).val(),
      }).appendTo($list);
      //if ($this.children('option').eq(i).is(':selected')){
      //  $('li[rel="' + $this.children('option').eq(i).val() + '"]').addClass('is-selected')
      //}
    }

    var $listItems = $list.children("li");

    $styledSelect.click(function (e) {
      e.stopPropagation();
      $("div.select-styled.active")
        .not(this)
        .each(function () {
          $(this).removeClass("active").next("ul.select-options").hide();
        });
      $(this).toggleClass("active").next("ul.select-options").toggle();
    });

    $listItems.click(function (e) {
      e.stopPropagation();
      $styledSelect.text($(this).text()).removeClass("active");
      $this.val($(this).attr("rel"));
      $list.hide();
    });

    $(document).click(function () {
      $styledSelect.removeClass("active");
      $list.hide();
    });
  });
}

function fakeSelectEvents() {
  $(document).on("keydown", ".autocomplete-input", function (event) {
    // console.log(event.key);
    if (event.key == "ArrowDown" || event.key == "ArrowUp") {
      if ($(".suggestion-container.focused").length) {
        goToNextInput(event.key);
      } else if (event.key == "ArrowDown") {
        $(".suggestion-container:first-child").addClass("focused");
      } else if (event.key == "ArrowUp") {
        $(".suggestion-container:last-child").addClass("focused");
        $(".suggestions-container.show-results").scrollTop(
          $(".suggestions-container.show-results")[0].scrollHeight
        );
      }
    } else if (event.key == "NumpadEnter" || event.key == "Enter") {
      event.preventDefault();
      if ($(".suggestion-container.focused").length) {
        suggestionContainerAction($(".suggestion-container.focused"));
        $(".suggestions-container").empty();
      }
    } else if (event.key == "Tab") {
      if ($(".suggestion-container.focused").length) {
        event.preventDefault();
        suggestionContainerAction($(".suggestion-container.focused"));
      }
      $(".suggestions-container").empty();
    }
  });
}

function goToNextInput(event) {
  let current = $(".suggestion-container.focused"),
    currentIndex = current.index(),
    nextIndex = event == "ArrowDown" ? currentIndex + 1 : currentIndex - 1,
    container = $(".suggestions-container.show-results"),
    containerIndex = $(".suggestions-container.show-results")[0],
    action = event == "ArrowDown" ? "next" : "prev";

  $(".suggestion-container").eq(currentIndex).removeClass("focused");
  if (event == "ArrowDown" && current.is(":last-child")) {
    // console.log("go to top");
    $(".suggestion-container:first-child").addClass("focused");
    container.scrollTop(0);
  } else if (event == "ArrowUp" && current.is(":first-child")) {
    // console.log("go to bottom");
    $(".suggestion-container:last-child").addClass("focused");
    container.scrollTop(containerIndex.scrollHeight);
  } else {
    $(".suggestion-container").eq(nextIndex).addClass("focused");
    checkPosition(current, containerIndex, container, action);
  }
}

function checkPosition(current, container, scrollElement, action) {
  let index = action == "next" ? current.index() + 2 : current.index(),
    indexHeight = current.outerHeight() * index,
    containerHeight = container.clientHeight,
    containerOffsetTop = Math.abs(container.scrollTop),
    scrollHeight = containerHeight + containerOffsetTop;

  if (indexHeight > scrollHeight && action == "next") {
    scrollElement.scrollTop(indexHeight - current.outerHeight());
  } else if (
    container.scrollHeight - containerOffsetTop - 2 <=
      container.scrollHeight - indexHeight &&
    action == "prev"
  ) {
    // console.log("scroll up");
    scrollElement.scrollTop(indexHeight - containerHeight);
  }
}
/*                              */
/*      END Modify Forms        */
/*                              */
