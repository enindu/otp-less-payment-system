package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"

	"github.com/rs/xid"
)

func generateKey(w http.ResponseWriter, r *http.Request) {
	// Set response headers
	w.Header().Add("Strict-Transport-Security", "max-age=63072000; includeSubDomains")
	w.Header().Add("Content-Type", "application/json")

	// Receive client request
	serverValidation := validateServer(r, ClientUsername, ClientPassword)
	if !serverValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, serverValidation.Data)
		w.WriteHeader(serverValidation.Code)
		json.NewEncoder(w).Encode(serverValidation)
		return
	}

	generateKey := &GenerateKey{}
	json.NewDecoder(r.Body).Decode(generateKey)
	dataValidation := validateData(generateKey)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Send middleware request
	data, _ := json.Marshal(&Message{
		Status: true,
		Code:   100,
		Data: &GenerateKey{
			DeviceID:            generateKey.DeviceID,
			AuthenticationToken: xid.New().String(),
		},
	})

	request, _ := http.NewRequest("POST", fmt.Sprintf("%s/generate-key", MiddlewareURL), bytes.NewBuffer(data))
	request.SetBasicAuth(AuthenticationUsername, AuthenticationPassword)
	request.Header.Add("Content-Type", "application/json")

	// Receive middleware response
	response, _ := client.Do(request)
	responseBinary, _ := io.ReadAll(response.Body)
	response.Body.Close()

	message := &Message{}
	json.Unmarshal(responseBinary, message)
	if !message.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	// Send client response
	w.WriteHeader(message.Code)
	json.NewEncoder(w).Encode(message)
}
