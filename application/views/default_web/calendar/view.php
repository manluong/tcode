
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
		list-style: none;
		padding:3px;
		margin:1px 0;
	}


	#calendar_list li.cal_0 { background-color:#36C; color:#FFF; }
	#calendar_list li.cal_1 { background-color:#C36; color:#FFF; }
	#calendar_list li.cal_2 { background-color:#3A6; color:#FFF; }
	#calendar_list li.cal_3 { background-color:#C63; color:#FFF; }


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

	#event_table tr td { padding:3px; }
</style>



<div id="container_calendar">

	<div id="calendar_options">

		<ul id="calendar_list">
		<?php
			foreach($calendars AS $cal) {
				$color = ($cal['calendar_color'] == NULL)
							? $cal['calendar_order']
							: $cal['calendar_color'];
				echo '<li class="cal_',$color,'">',$cal['display_name'],'</li>';
			}
		?>
		</ul>

	</div>

	<div id="calendar"></div>
</div>



<div id="create_event_form" title="Create new event">
	<form id="form_event">

		<table id="event_table">
			<tr>
				<td><label for="calendar_id">Calendar</label></td>
				<td>
					<select name="calendar_id" id="calendar_id">
					<?php
						foreach($calendars AS $cal) {
							echo '<option value="',$cal['id'],'">',$cal['display_name'],'</option>';
						}
					?>
					</select>
				</td>
			</tr>

			<tr>
				<td><label for="event_title">Event Title</label></td>
				<td><input type="text" name="event_title" id="event_title" style="width: 255px;" /></td>
			</tr>

			<tr>
				<td><label for="event_date_start">Start Date</label></td>
				<td><input type="text" name="event_date_start" id="event_date_start" class="datepicker" style="width: 200px;" /></td>
			</tr>

			<tr>
				<td><label for="event_date_end">End Date</label></td>
				<td><input type="text" name="event_date_end" id="event_date_end" class="datepicker" style="width: 200px;" /></td>
			</tr>

			<tr>
				<td><label for="event_allday">All Day</label></td>
				<td><input type="checkbox" name="event_allday" id="event_allday" value="1" /></td>
			</tr>

			<tr>
				<td><label for="event_memo">Memo</label></td>
				<td><input type="textarea" name="event_memo" id="event_memo" style="width:200px; height:100px;" /></td>
			</tr>
		</table>

	</form>
</div>

<script>
	var estart, eend, eallDay;

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

	var calSources = [
	<?php
		$output = array();
		foreach($calendars AS $cal) {
			$temp = '';

			$color = ($cal['calendar_color'] == NULL)
						? $cal['calendar_order']
						: $cal['calendar_color'];

			$temp .= '{';
			$temp .= 'url: "/calendar/ajax_get_events",';
			$temp .= 'data:';
				$temp .= '{';
				$temp .= 'calendar_id:'.$cal['id'];
				$temp .= '},';
			$temp .= 'color: bgcolor['.$color.'],';
			$temp .= 'textColor: fontcolor['.$color.']';
			$temp .= '}';
			$output[] = $temp;
		}
		echo implode(',',$output);
	?>
	];

	$(document).ready(function() {
		$.datepicker.setDefaults({
			dateFormat: 'yy-mm-dd'
		});
		$.timepicker.setDefaults({
			timeFormat: 'h:mm',
			stepMinute: 5
		});

		$('.datepicker').datetimepicker();


        var eventtitle = $( "#event_title" )


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
			select: function(start, end, allDay) {
                estart=start;
                eend=end;
                eallDay=allDay;

				$('#event_date_start').val(datestring(start));
				$('#event_date_end').val(datestring(end));

				$('#event_allday').prop('checked', allDay);
				$.uniform.update();

                $("#create_event_form").dialog("open");
			},
			editable: true,
			eventSources: calSources
		});


		$( "#create_event_form" ).dialog({
			autoOpen: false,
			height: 400,
			width: 450,
			modal: true,
            resizable: false,
            hide: 'fade',
            show: 'fade',
			buttons: {
				Cancel: function() {
					$( this ).dialog( "close" );
				},

				"Create Event": function() {
					var bValid = true;
					eventtitle.removeClass( "ui-state-error" );

					bValid = bValid && eventtitle.val() != '';

					if ( bValid ) {
                        var title = eventtitle.val();
                        calendar.fullCalendar('renderEvent',
                            {
                                title: title,
                                start: estart,
                                end: eend,
                                allDay: eallDay
                            },
                            true // make the event "stick"
                        );

						$.post(
							'/calendar/ajax_save_event',
							$('#form_event').serializeArray(),
							function(resp) {

							},
							'json'
						);

                        calendar.fullCalendar('unselect');
						$( this ).dialog( "close" );
					}
				}

			},
            close: function() {
                eventtitle.val("").removeClass( "ui-state-error" );
                estart=eend=eallDay=null;
            }
		});







	});

	function datestring(MyDate) {
		if (eallDay) {
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

</script>