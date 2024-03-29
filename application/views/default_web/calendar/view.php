<script type="text/javascript" src="/resources/addon/jquery.fullcalendar.min.js"></script>
<link rel="stylesheet" href="/resources/addon/jquery.fullcalendar.css" />
<link rel="stylesheet" href="/resources/addon/jqueryui/aristo/ui.css" />

<style>
	#calendar .fc-sun, #calendar .fc-sat {
		background-color:#EEE;
	}

	#container_calendar {

	}
	#calendar_options {
		float:left;
		width:190px;
		padding:50px 5px 5px 5px;
	}
	#calendar {
		overflow-x:hidden;
	}

	#calendar_list {
		padding-left:0;
	}
	#calendar_list li {
		cursor:pointer;
		padding:3px;
		margin:1px 0;
	}

	#calendar_list a {
		color:#FFF;
	}


	#calendar_list li.cal_0 { background-color:#36C; border:solid 1px #36C; color:#FFF; }
	#calendar_list li.cal_1 { background-color:#C36; border:solid 1px #C36; color:#FFF; }
	#calendar_list li.cal_2 { background-color:#3A6; border:solid 1px #3A6; color:#FFF; }
	#calendar_list li.cal_3 { background-color:#C63; border:solid 1px #C63; color:#FFF; }

	#calendar_list li.unselected { background-color:#FFF; color:#333; }


	/* css for timepicker */
	.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
	.ui-timepicker-div dl { text-align: left; }
	.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
	.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
	.ui-timepicker-div td { font-size: 90%; }
	.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

	/* fix clicking position problem on week/day view*/
	td.ui-widget-content { padding: 0 !important; }
	th.ui-widget-header { padding: 0 !important; }

</style>



<div id="container_calendar">

	<div id="calendar_options">

		<ul id="calendar_list" class="unstyled">
		<?php
			foreach($calendars AS $cal) {
				$color = ($cal['calendar_color'] == NULL)
							? $cal['calendar_order']
							: $cal['calendar_color'];
				echo '<li class="cal_',$color,'">';
					echo '<div class="cal_showhide" data-cal_id=',$cal['id'],'>';
					echo $cal['display_name'];
					echo '</div>';
					echo '<ul class="hide unstyled">';
						echo '<li><a href="webcal://'.str_replace('http://','',site_url()).'ical/index/',encode_id($cal['id']),'/',encode_id($cuid),'">Add to iCal</a></li>';
					echo '</ul>';
				echo '</li>';
			}
		?>
		</ul>

	</div>

	<div id="calendar"></div>
</div>



<div id="create_event_form" class="modal hide">


		<div class="modal-header">
			<a class="close" data-dismiss="modal">×</a>
			<h3>Create new event</h3>
		</div>

		<div class="modal-body">
			<form id="form_event" class="form-horizontal">
			<input type="hidden" name="event_id" id="event_id" value="" />


				<div class="control-group">
					<label class="control-label" for="calendar_id">Calendar</label>
					<div class="controls">
						<select name="calendar_id" id="calendar_id">
						<?php
							foreach($calendars AS $cal) {
								echo '<option value="',$cal['id'],'">',$cal['display_name'],'</option>';
							}
						?>
						</select>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="event_title">Event Title</label>
					<div class="controls">
						<input type="text" name="event_title" id="event_title" style="width: 255px;" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="event_date_start">Start Date</label>
					<div class="controls">
						<input type="text" name="event_date_start" id="event_date_start" class="datepicker" style="width: 200px;" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="event_date_end">End Date</label>
					<div class="controls">
						<input type="text" name="event_date_end" id="event_date_end" class="datepicker" style="width: 200px;" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="event_allday">All Day</label>
					<div class="controls">
						<input type="checkbox" name="event_allday" id="event_allday" value="1" />
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="event_memo">Memo</label>
					<div class="controls">
						<input type="textarea" name="event_memo" id="event_memo" style="width:200px; height:100px;" />
					</div>
				</div>

			</form>
		</div>

		<div class="modal-footer">
			<a href="#" class="btn" data-dismiss="modal">Close</a>
			<a href="#" class="btn btn-primary" id="create_event_submit">Save changes</a>
		</div>
</div>



<script>
	var is_update = false;

	var bgcolor = new Array();
	bgcolor[0] = '#36C';
	bgcolor[1] = '#C36';
	bgcolor[2] = '#3A6';
	bgcolor[3] = '#C63';

	var fontcolor = new Array();
	fontcolor[0] = '#FFF';
	fontcolor[1] = '#FFF';
	fontcolor[2] = '#FFF';
	fontcolor[3] = '#FFF';

	var calSources = new Array();
	<?php
		foreach($calendars AS $cal) {
			$temp = 'calSources['.$cal['id'].'] = ';

			$color = ($cal['calendar_color'] == NULL)
						? $cal['calendar_order']
						: $cal['calendar_color'];

			$temp .= '{';
			$temp .= 'url: "/calendar/ajax_get_events/'.$cal['id'].'",';
			$temp .= 'data:';
				$temp .= '{';
				$temp .= 'calendar_id:'.$cal['id'];
				$temp .= '},';
			$temp .= 'color: bgcolor['.$color.'],';
			$temp .= 'textColor: fontcolor['.$color.']';
			$temp .= '};';
			echo $temp;
		}
	?>

	$(document).ready(function() {
		$.datepicker.setDefaults({
			dateFormat: 'yy-mm-dd'
		});

		$.timepicker.setDefaults({
			timeFormat: 'h:mm',
			stepMinute: 5
		});

		$('.datepicker').datetimepicker();

		$('#create_event_form').modal({
			'backdrop': false,
			'show':false
		});

		$('#calendar_list li').on('hover', function(e) {
			$(this).find('ul').slideDown(300);
		}).on('mouseleave', function(e) {
			$(this).find('ul').slideUp(300);
		});


        var eventtitle = $('#event_title');

		var calendar = $('#calendar').fullCalendar({
            theme: true,
			aspectRatio: 1.8,
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
            selectable: true,
			selectHelper: true,
			editable: true,
			eventSources: calSources,
			select: function(start, end, allDay) {
				is_update = false;

				$('#event_date_start').val(datestring(start, allDay));
				$('#event_date_end').val(datestring(end, allDay));
				$('#event_allday').prop('checked', allDay);

				$('#event_title').val('');
				$('#event_memo').val('');

                $('#create_event_form').modal('show');
		},
			eventDrop: function(event, delta_day, delta_min, all_day, revert_func) {
				update_event(event, delta_day, delta_min, all_day, revert_func);
			},
			eventResize: function(event, delta_day, delta_min, revert_func) {
				update_event(event, delta_day, delta_min, false, revert_func);
			},
			eventClick: function(event, jsevent, view) {
				is_update = true;

				$('#event_id').val(event.id);
				$('#calendar_id').val(event.calendar_id);
				$('#event_title').val(event.title);
				$('#event_date_start').val(datestring(event.start, event.allDay));
				$('#event_date_end').val(datestring(event.end, event.allDay));
				$('#event_memo').val(event.memo);
				$('#event_allday').prop('checked', event.allDay);

				$('#create_event_form').modal('show');
			}
		});

		$('#create_event_submit').on('click', function() {
			var bValid = true;
			bValid = bValid && eventtitle.val() != '';

			if (bValid) {
				if (is_update) {
					var ajax_url = '/calendar/ajax_update_event';
				} else {
					var ajax_url = '/calendar/ajax_save_event';
				}

				$.post(
					ajax_url,
					$('#form_event').serializeArray(),
					function(resp) {},
					'json'
				);

				calendar.fullCalendar('refetchEvents');
				calendar.fullCalendar('unselect');
				$('#create_event_form').modal('hide');
			}
		});


		$('#calendar_list li div.cal_showhide').click(function() {
			var li = $(this);
			var cal_id = li.attr('data-cal_id');

			if (li.parent().hasClass('unselected')) {
				li.parent().removeClass('unselected');
				calendar.fullCalendar('addEventSource', calSources[cal_id]);
			} else {
				li.parent().addClass('unselected');
				calendar.fullCalendar('removeEventSource', calSources[cal_id]);
			}
		});



	});

	function datestring(MyDate, allDay) {
		if (MyDate == null) return '';

		if (allDay) {
			return MyDate.getFullYear() + '-'
				+ ('0' + (MyDate.getMonth()+1)).slice(-2) + '-'
				+ ('0' + MyDate.getDate()).slice(-2);
		} else {
			return MyDate.getFullYear() + '-'
				+ ('0' + (MyDate.getMonth()+1)).slice(-2) + '-'
				+ ('0' + MyDate.getDate()).slice(-2) + ' '
				+ ('0' + MyDate.getHours()).slice(-2) + ':'
				+ ('0' + MyDate.getMinutes()).slice(-2);

		}
	}

	function update_event(event, delta_day, delta_min, all_day, revert_func) {
		$.post(
			'/calendar/ajax_update_event_dragdrop',
			{ event_id:event.id, delta_day:delta_day, delta_min:delta_min, all_day:all_day },
			function(resp) { },
			'json'
		);
	}

</script>