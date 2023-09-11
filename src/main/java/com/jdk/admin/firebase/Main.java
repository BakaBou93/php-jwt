package com.jdk.admin.firebase;

import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;

import com.google.auth.oauth2.GoogleCredentials;
import com.google.firebase.FirebaseApp;
import com.google.firebase.FirebaseOptions;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.auth.FirebaseAuthException;
import com.google.firebase.messaging.FirebaseMessaging;
import com.google.firebase.messaging.FirebaseMessagingException;
import com.google.firebase.messaging.Message;

public class Main {
    public static void main(String[] args) {
        try {
            FileInputStream serviceAccount = new FileInputStream("./push-notifications-3c140-f40e8cfb60d4.json");

            GoogleCredentials googleCredentials = GoogleCredentials.fromStream(serviceAccount);

            FirebaseOptions options = FirebaseOptions.builder()
                .setCredentials(googleCredentials)
                .build();

            // Initialize the default app
            FirebaseApp firebaseApp = FirebaseApp.initializeApp(options);

            // Retrieve services by passing the defaultApp variable...
            FirebaseAuth defaultAuth = FirebaseAuth.getInstance(firebaseApp);

            String token = defaultAuth.createCustomToken("test_uid");

            System.out.println("Token :" + token);

            // This registration token comes from the client FCM SDKs.
            String registrationToken = "dxC0GrLQqa5agBNnqIOLXy:APA91bEUy7lMR8Qsh2s5IGAX9mPHqkEb0N8y9IWW2-vuIB_K8TQsRhJN95lCTdAWNVdzAqolwi_zMjVe6N7JaNsOmtKZl_SyjIh9yrvdF5aVoyR6DTxixWRKlMfl1jXgCDrgwTyWq9rz";

            // See documentation on defining a message payload.
            Message message = Message.builder()
                    .putData("score", "850")
                    .putData("time", "2:45")
                    .setToken(registrationToken)
                    .build();

            // Send a message to the device corresponding to the provided
            // registration token.
            String response = FirebaseMessaging.getInstance().send(message);
            // Response is a message ID string.
            System.out.println("Successfully sent message: " + response);

        } catch (FileNotFoundException | FirebaseAuthException | FirebaseMessagingException e) {
            System.out.println(e.getMessage());
        } catch (IOException e) {
            System.out.println("IO: " + e.getMessage());
        }
    }
}