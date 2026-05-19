# Quick Link Specification for SAP Sailing Race Manager

Constants can be found in `com.sap.sailing.domain.common.BranchIOConstants.java`.

## Base URL

`https://racemanager-app.sapsailing.com/invite`

## Params

All parameters are optional.

| Name | Type | Comments |
| ---| --- | --- |
| server_url | string | URL |
| device_config_identifier | string | Device name/identifier |
| device_config_uuid | string | UUID of the device configuration |
| token | string | Access token |
| event_id | string | UUID of the event |
| course_area_uuid | string | UUID of the course area |
| priority | integer | 1 - Race Officer on Vessel<br>2 - Shore Control |

## Sample

https://racemanager-app.sapsailing.com/invite?server_url=https://dev.sapsailing.com/&device_config_identifier=D-Labs+Test&device_config_uuid=6fb0e2e3-23e6-4d2e-b66a-77d98feb7fdd&token=KY5G48LdOiTysCCiRfvFIVO87Ljbfzo3a4%2Bal63H%2Fvw%3D&event_id=8c3bdd16-a3d1-4a38-bf82-29d98ce18bec&course_area_uuid=fe96623a-d570-4dc5-8cc4-7fe8c5e7e2c0&priority=2
