db.RACE_LOGS.find({"RACE_LOG_EVENT.RACE_LOG_EVENT_CLASS": "RaceLogDeviceCompetitorMappingEvent"}).forEach(function(e) {
	if (e!= null){
		regattaExists = db.REGATTAS.find({"REGATTA_NAME" : e.RACE_LOG_IDENTIFIER.a}).size() > 0;
		var identifier_type;
		if (regattaExists) {
			print("regattaExists");
			identifier_type = "com.sap.sailing.domain.base.Regatta";			
		} else { 
			print("regatta doesnt exist");
			identifier_type = "com.sap.sailing.domain.leaderboard.FlexibleLeaderboard";
		}
		
		print("Inserting migrated event");
		var correspondingRegattaLogEvent = {    
			"_id":e._id,
			"REGATTA_LOG_EVENT":{  
	    		"TIME_AS_MILLIS":e.RACE_LOG_EVENT.TIME_AS_MILLIS,
	    		"REGATTA_LOG_EVENT_CREATED_AT":e.RACE_LOG_EVENT.RACE_LOG_EVENT_CREATED_AT,
	    		"REGATTA_LOG_EVENT_ID":e.RACE_LOG_EVENT.RACE_LOG_EVENT_ID,
	    		"REGATTA_LOG_EVENT_AUTHOR_NAME":e.RACE_LOG_EVENT.RACE_LOG_EVENT_AUTHOR_NAME,
	    		"REGATTA_LOG_EVENT_AUTHOR_PRIORITY":e.RACE_LOG_EVENT.RACE_LOG_EVENT_AUTHOR_PRIORITY,
	    		"REGATTA_LOG_EVENT_CLASS":"RegattaLogDeviceCompetitorMappingEvent",
	   			"DEVICE_ID":{  
	       			"DEVICE_TYPE":e.RACE_LOG_EVENT.DEVICE_ID.DEVICE_TYPE,
	       			"DEVICE_TYPE_SPECIFIC_ID":e.RACE_LOG_EVENT.DEVICE_ID.DEVICE_TYPE_SPECIFIC_ID,
	       			"DEVICE_STRING_REPRESENTATION":e.RACE_LOG_EVENT.DEVICE_ID.DEVICE_STRING_REPRESENTATION
	   			},
	   			"REGATTA_LOG_FROM":e.RACE_LOG_EVENT.RACE_LOG_FROM,
	  			"REGATTA_LOG_TO":e.RACE_LOG_EVENT.RACE_LOG_TO,
	   			"COMPETITOR_ID": e.RACE_LOG_EVENT.COMPETITOR_ID
	   			},
	   		"REGATTA_LOG_IDENTIFIER_TYPE":identifier_type,
	   		"REGATTA_LOG_IDENTIFIER_NAME":e.RACE_LOG_IDENTIFIER.a
		};
		db.REGATTA_LOGS.insert(correspondingRegattaLogEvent);	
		print("Removing");
		db.RACE_LOGS.remove({"_id":e._id},{justOne:true});
	}
})