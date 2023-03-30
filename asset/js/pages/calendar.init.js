/*
Template Name: Adminox - Responsive Bootstrap 4 Admin Dashboard
Author: CoderThemes
Version: 2.0.0
Website: https://coderthemes.com/
Contact: support@coderthemes.com
File: Calendar init js
*/

!(function ($) {
  "use strict";

  var CalendarApp = function () {
    this.$body = $("body");
    // (this.$modal = $("#event-modal")),
    (this.$modal = $("#add-category")),
      (this.$event = "#external-events div.external-event"),
      (this.$calendar = $("#calendar")),
      (this.$saveCategoryBtn = $(".save-category")),
      (this.$categoryForm = $("#add-category form")),
      (this.$extEvents = $("#external-events")),
      (this.$calendarObj = null);
  };

  /* on drop */

  //
  // modify
  /* on Move */
  /* (CalendarApp.prototype.onMove = function (eventObj, date) {
    console.log(eventObj);
    var d = new Date(date);
    var dateString = d.toDateString();

    Swal.fire(
      swal_setConfirm("ยืนยันการจอง", "จองรายการในวันที่ " + dateString)
    ).then((result) => {
      if (result.value) {
      }
    });
  }), */
  /* (CalendarApp.prototype.onSubmit = function (item_id, date) {
      let url = new URL(
        "calendar/ctl_manage/update_bill_booking",
        window.origin
      );

      let data = new FormData();
      data.append("item_id", item_id);
      data.append("booking_date", date);

      fetch(url, {
        method: "post",
        body: data,
      })
        .then((res) => res.json())
        .then((resp) => {
          return resp;
        });
    }), */
  //
  //

  /* on click on event */
  (CalendarApp.prototype.onEventClick = function (calEvent, jsEvent, view) {
    var $this = this;
    $this.$modal.modal({
      backdrop: "static",
    });
    var frm_method = $("#form_add").find("#method");
    var frm_item_id = $("#form_add").find("#item_id");
    frm_method.val("edit");
    frm_item_id.val(calEvent.id);

    var agent_name = "";
    if (calEvent.agent_name && calEvent.agent_name != null) {
      agent_name = calEvent.agent_name;
    }
    var agent_contact = "";
    if (calEvent.agent_contact && calEvent.agent_contact != null) {
      agent_contact = calEvent.agent_contact;
    }
    var remark = "";
    if (calEvent.remark && calEvent.remark != null) {
      remark = calEvent.remark;
    }

    var form = $(".block_btn");
    form
      .prepend(
        '<div class="form-group w-100 block_del"><button type="button" class="btn btn-warning btn-block btn-cancel waves-effect waves-light" data-id="' +
          calEvent.id +
          '">กลับไปรอจอง</button></div>'
      )
      .prepend(
        '<div class="form-group w-100 block_del"><button type="button" class="btn btn-danger btn-block btn-del waves-effect waves-light" data-id="' +
          calEvent.id +
          '">ลบรายการ</button></div>'
      );

    // console.log(calEvent);
    $this.$modal
      .find("[name=customer]")
      .val(calEvent.title)
      .end()
      .find("[name=agent_name]")
      .val(agent_name)
      .end()
      .find("[name=agent_contact]")
      .val(agent_contact)
      .end()
      .find("[name=totals]")
      .val(calEvent.totals)
      .end()
      .find("[name=remark]")
      .val(remark)
      .end()
      .find("#payment")
      .val(calEvent.payment_id)
      .trigger("change")
      .end()
      .find("#round")
      .val(calEvent.round_id)
      .trigger("change")
      .end()
      .find("#booking_date")
      .val(calEvent.booking_dateShow)
      .end()
      .find(".modal-body .block_btn")
      .prepend(form)
      .end();
    /* .find(".btn-del")
        .unbind("click")
        .click(function () {
          $this.$calendarObj.fullCalendar("removeEvents", function (ev) {
            return ev._id == calEvent._id;
          });
          $this.$modal.modal("hide");
        }); */

    $($this.$modal).on("hidden.bs.modal", function () {
      $(".block_del").remove();

      var frm_method = $("#form_add").find("#method");
      frm_method.val("");
    });

    return false;
    var $this = this;
    var form = $("<form></form>");
    form.append("<label>Change event name</label>");
    form.append(
      "<div class='input-group m-b-15'><input class='form-control' type=text value='" +
        calEvent.title +
        "' /><span class='input-group-append'><button type='submit' class='btn btn-success btn-md waves-effect waves-light'><i class='fa fa-check'></i> Save</button></span></div>"
    );
    $this.$modal.modal({
      backdrop: "static",
    });
    $this.$modal
      .find(".delete-event")
      .show()
      .end()
      .find(".save-event")
      .hide()
      .end()
      .find(".modal-body")
      .empty()
      .prepend(form)
      .end()
      .find(".delete-event")
      .unbind("click")
      .click(function () {
        $this.$calendarObj.fullCalendar("removeEvents", function (ev) {
          return ev._id == calEvent._id;
        });
        $this.$modal.modal("hide");
      });
    $this.$modal.find("form").on("submit", function () {
      calEvent.title = form.find("input[type=text]").val();
      $this.$calendarObj.fullCalendar("updateEvent", calEvent);
      $this.$modal.modal("hide");
      return false;
    });
  }),
    /* on select */
    (CalendarApp.prototype.onSelect = function (start, end, allDay) {
      var $this = this;
      $this.$modal.modal({
        backdrop: "static",
      });
      var d = new Date(start);
      var month = d.getMonth() + 1;
      var booking_date = `${d.getDate().toString().padStart(2, "0")}/${month
        .toString()
        .padStart(2, "0")}/${d.getFullYear()}`;

      $("#booking_date").val(booking_date);

      return false;
      var $this = this;
      $this.$modal.modal({
        backdrop: "static",
      });
      var form = $("<form></form>");
      form.append("<div class='row'></div>");
      form
        .find(".row")
        .append(
          "<div class='col-md-6'><div class='form-group'><label class='control-label'>Event Name</label><input class='form-control' placeholder='Insert Event Name' type='text' name='title'/></div></div>"
        )
        .append(
          "<div class='col-md-6'><div class='form-group'><label class='control-label'>Category</label><select class='form-control' name='category'></select></div></div>"
        )
        .find("select[name='category']")
        .append("<option value='bg-danger'>Danger</option>")
        .append("<option value='bg-success'>Success</option>")
        .append("<option value='bg-purple'>Purple</option>")
        .append("<option value='bg-primary'>Primary</option>")
        .append("<option value='bg-pink'>Pink</option>")
        .append("<option value='bg-info'>Info</option>")
        .append("<option value='bg-inverse'>Inverse</option>")
        .append("<option value='bg-warning'>Warning</option></div></div>");
      $this.$modal
        .find(".delete-event")
        .hide()
        .end()
        .find(".save-event")
        .show()
        .end()
        .find(".modal-body")
        .empty()
        .prepend(form)
        .end()
        .find(".save-event")
        .unbind("click")
        .click(function () {
          form.submit();
        });
      $this.$modal.find("form").on("submit", function () {
        var title = form.find("input[name='title']").val();
        var beginning = form.find("input[name='beginning']").val();
        var ending = form.find("input[name='ending']").val();
        var categoryClass = form
          .find("select[name='category'] option:checked")
          .val();
        if (title !== null && title.length != 0) {
          $this.$calendarObj.fullCalendar(
            "renderEvent",
            {
              title: title,
              start: start,
              end: end,
              allDay: false,
              className: categoryClass,
            },
            true
          );
          $this.$modal.modal("hide");
        } else {
          alert("You have to give a title to your event");
        }
        return false;
      });
      $this.$calendarObj.fullCalendar("unselect");
    }),
    (CalendarApp.prototype.enableDrag = function () {
      //init events
      $(this.$event).each(function () {
        // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
        // it doesn't need to have a start or end
        var eventObject = {
          title: $.trim($(this).text()), // use the element's text as the event title
        };
        // store the Event Object in the DOM element so we can get to it later
        $(this).data("eventObject", eventObject);
        // make the event draggable using jQuery UI
        $(this).draggable({
          zIndex: 999,
          revert: true, // will cause the event to go back to its
          revertDuration: 0, //  original position after the drag
        });
      });
    });

  function get_bill_booking() {
    let url = new URL("calendar/ctl_manage/fetch_bill_booking", window.origin);
    return new Promise((resolve, reject) => {
      fetch(url)
        .then((res) => res.json())
        .then((resp) => {
          resolve(resp);
        });
    });
  }

  /* Initializing */
  (CalendarApp.prototype.init = function () {
    this.enableDrag();
    /*  Initialize the calendar  */
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();
    var form = "";
    var today = new Date($.now());

    var $this = this;

    // async
    displayShow();
    async function displayShow() {
      let dataBill = await get_bill_booking();

      let dateDefault = [];
      if (dataBill) {
        dataBill.forEach(function (item, index) {
          let setArray = [];

          let start = item.BOOKING_DATE + " " + item.TIME_START;
          let end = item.BOOKING_DATE + " " + item.TIME_END;
          let booking_dateShow = null;
          if (item.BOOKING_DATE || item.BOOKING_DATE != null) {
            let dsplit = item.BOOKING_DATE.split("-");
            booking_dateShow = dsplit[2] + "/" + dsplit[1] + "/" + dsplit[0];
          }

          let typeing;
          switch (item.PAYMENT_ID) {
            case "4":
              typeing = "warning";
              break;
            case "5":
              typeing = "success";
              break;
          }

          setArray = {
            start: start,
            id: item.ID,
            end: end,
            title: item.CUSTOMER_NAME,
            totals: item.TOTALS,
            agent_name: item.AGENT_NAME,
            agent_contact: item.AGENT_CONTACT,
            payment_id: item.PAYMENT_ID,
            booking_date: item.BOOKING_DATE,
            booking_dateShow: booking_dateShow,
            remark: item.REMARK,
            round_id: item.ROUND_ID,
            time_start: item.TIME_START,
            time_end: item.TIME_END,
            className: "bg-" + typeing,
          };
          dateDefault.push(setArray);
        });
      }
      // console.log(dateDefault);
      // code working
      $this.$calendarObj = $this.$calendar.fullCalendar({
        slotDuration:
          "00:15:00" /* If we want to split day time each 15minutes */,
        minTime: "08:00:00",
        maxTime: "19:00:00",
        defaultView: "month",
        handleWindowResize: true,
        height: $(window).height() - 200,
        header: {
          left: "prev,next today",
          center: "title",
          right: "month,agendaWeek,agendaDay",
        },
        events: function (start, end, tz, callback) {
          callback(dateDefault);
        },
        timeFormat: "H:mm",

        editable: true,
        droppable: true, // this allows things to be dropped onto the calendar !!!
        eventLimit: true, // allow "more" link when too many events
        selectable: true,
        drop: function (date) {
          $this.onDrop($(this), date);
        },
        select: function (start, end, allDay) {
          $this.onSelect(start, end, allDay);
        },
        eventClick: function (calEvent, jsEvent, view) {
          $this.onEventClick(calEvent, jsEvent, view);
        },
        eventDrop: function (calEvent, delta, revertFunc) {
          $this.onMove(calEvent, revertFunc);
        },
        eventResize: function (info) {
          console.log(info);
        },
      });
    }

    /* var defaultEvents = [
      {
        title: "See John Deo",
        start: today,
        end: today,
        className: "bg-success",
      },
      {
        title: "Meet John Deo",
        start: new Date($.now() + 168000000),
        className: "bg-info",
      },
      {
        title: "Buy a Theme",
        start: new Date($.now() + 338000000),
        className: "bg-primary",
      },
    ]; */

    //on new event
    this.$saveCategoryBtn.on("click", function () {
      var categoryName = $this.$categoryForm
        .find("input[name='category-name']")
        .val();
      var categoryColor = $this.$categoryForm
        .find("select[name='category-color']")
        .val();
      if (categoryName !== null && categoryName.length != 0) {
        $this.$extEvents.append(
          '<div class="external-event bg-' +
            categoryColor +
            '" data-class="bg-' +
            categoryColor +
            '" style="position: relative;"><i class="mdi mdi-checkbox-blank-circle mr-2 vertical-middle"></i>' +
            categoryName +
            "</div>"
        );
        $this.enableDrag();
      }
    });
  }),
    //init CalendarApp
    ($.CalendarApp = new CalendarApp()),
    ($.CalendarApp.Constructor = CalendarApp);
})(window.jQuery),
  //initializing CalendarApp
  (function ($) {
    "use strict";
    $.CalendarApp.init();
  })(window.jQuery);
