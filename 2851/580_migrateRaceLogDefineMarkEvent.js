db.RACE_LOGS.find({"RACE_LOG_EVENT.RACE_LOG_EVENT_CLASS": "RaceLogDefineMarkEvent"}).forEach(function(e) {
	if (e!= null){

		regattaExists = db.REGATTAS.find({"REGATTA_NAME" : e.RACE_LOG_IDENTIFIER.a}).size() > 0;
		regattaLogEventForMarkExists = db.REGATTA_LOGS.find({"REGATTA_LOG_EVENT.REGATTA_LOG_MARK.MARK_ID" : e.RACE_LOG_EVENT.RACE_LOG_MARK.MARK_ID, "REGATTA_LOG_EVENT.REGATTA_LOG_EVENT_CLASS": "RegattaLogDefineMarkEvent"}).size() > 0;
		var identifier_type;
		if (regattaExists) {
			print("regattaExists");
			identifier_type = "com.sap.sailing.domain.base.Regatta";
			
		} else { 
			print("regatta doesnt exist");
			identifier_type = "com.sap.sailing.domain.leaderboard.FlexibleLeaderboard"
		}

		if (!regattaLogEventForMarkExists){
				print("Inserting migrated event");
				var correspondingRegattaLogEvent = {    
	   			"_id":e._id,
	   			"REGATTA_LOG_EVENT":{  
	      			"TIME_AS_MILLIS":e.RACE_LOG_EVENT.TIME_AS_MILLIS,
	      			"REGATTA_LOG_EVENT_CREATED_AT":e.RACE_LOG_EVENT.RACE_LOG_EVENT_CREATED_AT,
	      			"REGATTA_LOG_EVENT_ID":e.RACE_LOG_EVENT.RACE_LOG_EVENT_ID,
	      			"REGATTA_LOG_EVENT_AUTHOR_NAME":e.RACE_LOG_EVENT.RACE_LOG_EVENT_AUTHOR_NAME,
	      			"REGATTA_LOG_EVENT_AUTHOR_PRIORITY":e.RACE_LOG_EVENT.RACE_LOG_EVENT_AUTHOR_PRIORITY,
	      			"REGATTA_LOG_EVENT_CLASS":"RegattaLogDefineMarkEvent",
		      		"REGATTA_LOG_MARK":{  
		         		"MARK_ID":e.RACE_LOG_EVENT.RACE_LOG_MARK.MARK_ID,
		         		"MARK_COLOR":e.RACE_LOG_EVENT.RACE_LOG_MARK.MARK_COLOR,
		         		"MARK_NAME":e.RACE_LOG_EVENT.RACE_LOG_MARK.MARK_NAME,
		         		"MARK_PATTERN":e.RACE_LOG_EVENT.RACE_LOG_MARK.MARK_PATTERN,
		         		"MARK_SHAPE":e.RACE_LOG_EVENT.RACE_LOG_MARK.MARK_SHAPE,
		         		"MARK_TYPE":e.RACE_LOG_EVENT.RACE_LOG_MARK.MARK_TYPE
		      		}
	   			},
	   			"REGATTA_LOG_IDENTIFIER_TYPE":identifier_type,
	   			"REGATTA_LOG_IDENTIFIER_NAME":e.RACE_LOG_IDENTIFIER.a
				};
				db.REGATTA_LOGS.insert(correspondingRegattaLogEvent);	
		} else {
			print("Skipping due to existing event for mark id")
		}

			print("Removing");
			db.RACE_LOGS.remove({"_id":e._id},{justOne:true});
	}
})