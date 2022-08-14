$(document).ready(function () {
   inputMask();
   createDatepicker();
   validateInputs();
   autoFillBorrower();
   autoFillNewLoan();
   setToZero();

   // Toggle Modal Functions START
   openModal(".create-borrower", ".form-modal", "submit-create", openCreate);
   openModal(".edit-btn", ".form-modal", "submit-edit", openEdit);
   openModal(".delete-borrower", "#deleteBorrower", "", openDelete);

   openModal(".btn-new-loan", ".form-modal", "submit-loan", openCreate);

   openModal(
      ".open-payment-modal",
      "#paymentModal",
      "submit-payment",
      openCreate
   );

   closeModal();
   // Toggle Modal Functions END

   // Modal Submit Functions START
   submitForm(".submit-create", ".action-form", "create-borrower.php");
   submitForm(
      ".submit-edit",
      ".action-form",
      "edit-borrower.php",
      editBorrowerAction
   );

   submitForm(".submit-loan", ".action-form", "create-loan.php");

   submitForm(".submit-payment", ".action-form", "add-payment.php");
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
      },
   },
   confirmMessages: {
      borrower: {
         delete: "Are you sure you want to delete",
      },
   },
};

/*                                */
/*    Modal Related Functions     */
/*                                */

function openModal(
   buttonName,
   modalName,
   submitBtnClass,
   modalFunction = false
) {
   $(document).on("click", buttonName, function (event) {
      event.preventDefault();
      $(modalName).find(".btn-action").addClass(submitBtnClass);
      if (modalFunction) {
         modalFunction(buttonName, modalName, $(this));
      }
      $(modalName).modal("toggle");
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
      var selectBox = document.getElementById("type");
      var selectedValue = selectBox.options[selectBox.selectedIndex].value;
      if (selectedValue == "Pass") {
         $("#payment").val(0);
         document.getElementById("payment").readOnly = true;
      } else {
         var paymentAmount = $("#payment").val();
         if (paymentAmount != 0) {
            $("#payment").val(paymentAmount);
         } else {
            $("#payment").val("");
         }
         document.getElementById("payment").readOnly = false;
      }
   }
}

function autoFillBorrower() {
   $("#namesearch").on("input focus", function () {
      $(this).siblings(".suggestions-container").addClass("show-results");
      var name = $("#namesearch").val(),
         thisInput = $(this);
      $.ajax({
         url: "../ajax-calls/suggestions.php",
         method: "POST",
         data: {
            suggestion: name,
         },
         dataType: "json",
         beforeSend: function () {},
         success: function (data, xhr, success) {
            thisInput.siblings(".suggestions-container").html(data);
            console.log(data);
            console.log(xhr);
            console.log(success);
         },
         error: function (response, xhr, data) {
            console.log(xhr);
            thisInput
               .siblings(messages.confirmMessages.borrower.noresults)
               .html(data);
         },
      });
   });

   $(document).on("click", function (e) {
      let target = $(e.target);
      if (!target.is(".autocomplete-input")) {
         $(".suggestions-container").removeClass("show-results");
      }
   });

   $(document).on("click", ".suggestion-container", function () {
      let datePickerSelector = $(this)
            .parents(".action-form")
            .find(".set-min-date"),
         setMinDate = $(this).data("releasedate");

      $(this).parent().siblings(".autocomplete-input").val($(this).text());
      $(this).parent().siblings(".borrower-id").val($(this).data("borrower"));
      fillInputs($(this).data("borrower"));
      clearErrors($(this).parent().siblings(".autocomplete-input"));
      checkEmptyInput($(this).parent().siblings(".autocomplete-input"));
      datePickerSelector.datepicker("option", "minDate", new Date(setMinDate));
      $("#payment").focus();
   });
}

function autoFillNewLoan() {
   $("#newloansearch").on("input focus", function () {
      $(this).siblings(".suggestions-container").addClass("show-results");
      var name = $("#newloansearch").val(),
         thisInput = $(this);
      $.ajax({
         url: "../ajax-calls/suggestions-new-loan.php",
         method: "POST",
         data: {
            suggestion: name,
         },
         dataType: "json",
         beforeSend: function () {},
         success: function (data, xhr, success) {
            thisInput.siblings(".suggestions-container").html(data);
            console.log(data);
            console.log(xhr);
            console.log(success);
         },
         error: function (response, xhr, data) {
            console.log(xhr);
            thisInput
               .siblings(messages.confirmMessages.borrower.noresults)
               .html(data);
         },
      });
   });

   $(document).on("click", function (e) {
      let target = $(e.target);
      if (!target.is(".autocomplete-input")) {
         $(".suggestions-container").removeClass("show-results");
      }
   });

   $(document).on("click", ".suggestion-container", function () {
      let datePickerSelector = $(this)
            .parents(".action-form")
            .find(".set-min-date"),
         setMinDate = $(this).data("releasedate");

      $(this).parent().siblings(".autocomplete-input").val($(this).text());
      $(this).parent().siblings(".borrower-id").val($(this).data("borrower"));
      fillInputs($(this).data("borrower"));
      clearErrors($(this).parent().siblings(".autocomplete-input"));
      checkEmptyInput($(this).parent().siblings(".autocomplete-input"));
      datePickerSelector.datepicker("option", "minDate", new Date(setMinDate));
      $("#payment").focus();
   });
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
         payment.placeholder = borrowerDetails[0]["amortization"].toFixed(2);
      },
      error: function (xghr, status, error) {
         console.log(xghr);
         console.log(status);
         console.log(error);
      },
   });
}

function editBorrowerAction(data, newValues, rowId) {
   let newData = JSON.parse(data),
      borrower =
         newData.firstname + " " + newData.middlename + " " + newData.lastname,
      contactno = newData.contactno,
      address = newData.address,
      comaker = newData.comaker,
      comakerno = newData.comakerno;

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
}

function openCreate() {
   if ($(".action-form").length) {
      let noResetArr = [],
         count = 0;
      $(".action-form .no-reset").each(function () {
         noResetArr.push($(this).val());
      });
      $(".action-form")[0].reset();
      $(".action-form .no-reset").each(function () {
         $(this).val(noResetArr[count]);
         count++;
      });
   }
}

function submitForm(submitBtn, thisForm, ajaxFile, ajaxAction) {
   $(document).on("click", submitBtn, function (event) {
      event.preventDefault();
      let form = $(thisForm),
         formValues = form.serialize(),
         newValues = form.serializeArray(),
         rowId = $(this)
            .parents(".modal-content")
            .find('input[name="data-row"]')
            .val();

      if (validateForm(thisForm))
         $.ajax({
            url: "../ajax-calls/" + ajaxFile,
            method: "POST",
            data: formValues,
            dataType: "html",
            beforeSend: function () {},
            success: function (data) {
               if (ajaxAction) ajaxAction(data, newValues, rowId);
               console.log(formValues);
               $(".form-modal .modal-content").fadeOut(300, function (param) {
                  $(".success-message").fadeIn(300, function () {
                     setTimeout(function () {
                        if ($("body").hasClass("modal-open"))
                           $(".form-modal").modal("hide");
                     }, 2000);
                  });
               });
            },
            error: function (response, xhr, data) {
               console.log("error");
               console.log(response);
               console.log(xhr);
               console.log(data);
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
   $(thisInput).next("span").remove();
}

/*                           */
/*      END Validations      */
/*                           */

/*                                */
/*      START Modify Forms        */
/*                                */ 4;

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
                     y =
                        this.parentNode.getElementsByClassName(
                           "same-as-selected"
                        );
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
                     y =
                        this.parentNode.getElementsByClassName(
                           "same-as-selected"
                        );
                     yl = y.length;
                     for (k = 0; k < yl; k++) {
                        y[k].removeAttribute("class");
                     }
                     this.setAttribute("class", "same-as-selected");
                     $(".ui-datepicker-year")
                        .val(this.innerHTML)
                        .trigger("change");
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

/*                              */
/*      END Modify Forms        */
/*                              */
