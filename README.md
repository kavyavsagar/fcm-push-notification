# FCM Push Notification
Firebase cloud messaging is a cross platform messaging solution across ios, android or web. The library of fcm support some languages such as NodeJs, Python, Java etc. We can directly use that without any pain.

If our server api is done in PHP, we can use this method to integrate FCM for push notification. In this mehod, i'm just describing the basic work flow of FCM.

Any device (ios/android) get registered which have device token and its saved into our db using an api. When we send a notification to single or multiple users which can be done with this token. Please check the source code for more clarity.

I don't mention the creation of firebase app creation and get a Server Key from there https://console.firebase.google.com/u/0/ You can refer https://firebase.google.com/docs/cloud-messaging for more details.


