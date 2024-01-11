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

func register(w http.ResponseWriter, r *http.Request) {
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

	register := &Register{}
	json.NewDecoder(r.Body).Decode(register)
	dataValidation := validateData(register)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Send core request
	data, _ := json.Marshal(&Message{
		Status: true,
		Code:   100,
		Data:   register,
	})

	request, _ := http.NewRequest("POST", fmt.Sprintf("%s/register", CoreURL), bytes.NewBuffer(data))
	request.SetBasicAuth(MiddlewareUsername, MiddlewarePassword)
	request.Header.Add("Content-Type", "application/json")

	// Receive core response
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

func generateKey(w http.ResponseWriter, r *http.Request) {
	// Set response headers
	w.Header().Add("Strict-Transport-Security", "max-age=63072000; includeSubDomains")
	w.Header().Add("Content-Type", "application/json")

	// Receive authentication request
	serverValidation := validateServer(r, AuthenticationUsername, AuthenticationPassword)
	if !serverValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, serverValidation.Data)
		w.WriteHeader(serverValidation.Code)
		json.NewEncoder(w).Encode(serverValidation)
		return
	}

	message := &Message{}
	json.NewDecoder(r.Body).Decode(message)
	data, _ := json.Marshal(message.Data)

	generateKey := &GenerateKey{}
	json.Unmarshal(data, generateKey)
	dataValidation := validateData(generateKey)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Read and update database
	database := connectDatabase()
	token := &Token{}
	tokenExists := database.Where("device_id = @DeviceID", generateKey).First(token)
	if tokenExists.Error == nil {
		database.Unscoped().Delete(token)
	}

	middlewareToken := xid.New().String()
	database.Create(&Token{
		DeviceID:            generateKey.DeviceID,
		AuthenticationToken: generateKey.AuthenticationToken,
		MiddlewareToken:     middlewareToken,
	})

	// Send authentication response
	message = &Message{
		Status: true,
		Code:   200,
		Data:   []string{middlewareToken},
	}

	w.WriteHeader(message.Code)
	json.NewEncoder(w).Encode(message)
}

func getBalance(w http.ResponseWriter, r *http.Request) {
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

	getInformation := &GetInformation{}
	json.NewDecoder(r.Body).Decode(getInformation)
	dataValidation := validateData(getInformation)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Send core request
	data, _ := json.Marshal(&Message{
		Status: true,
		Code:   100,
		Data:   getInformation,
	})

	request, _ := http.NewRequest("POST", fmt.Sprintf("%s/get-balance", CoreURL), bytes.NewBuffer(data))
	request.SetBasicAuth(MiddlewareUsername, MiddlewarePassword)
	request.Header.Add("Content-Type", "application/json")

	// Receive core response
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

func getTransactions(w http.ResponseWriter, r *http.Request) {
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

	getInformation := &GetInformation{}
	json.NewDecoder(r.Body).Decode(getInformation)
	dataValidation := validateData(getInformation)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Send core request
	data, _ := json.Marshal(&Message{
		Status: true,
		Code:   100,
		Data:   getInformation,
	})

	request, _ := http.NewRequest("POST", fmt.Sprintf("%s/get-transactions", CoreURL), bytes.NewBuffer(data))
	request.SetBasicAuth(MiddlewareUsername, MiddlewarePassword)
	request.Header.Add("Content-Type", "application/json")

	// Receive core response
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

func transferMoneySameBank(w http.ResponseWriter, r *http.Request) {
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

	transferMoney := &TransferMoney{}
	json.NewDecoder(r.Body).Decode(transferMoney)
	dataValidation := validateData(transferMoney)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Read database
	database := connectDatabase()
	token := &Token{}
	tokenExists := database.Where("device_id = @SenderDeviceID", transferMoney).First(token)
	if tokenExists.Error != nil {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"no token found"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	if transferMoney.MiddlewareToken != token.MiddlewareToken {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"invalid token"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	// Send core request
	data, _ := json.Marshal(&Message{
		Status: true,
		Code:   100,
		Data:   transferMoney,
	})

	request, _ := http.NewRequest("POST", fmt.Sprintf("%s/transfer-money-same-bank", CoreURL), bytes.NewBuffer(data))
	request.SetBasicAuth(MiddlewareUsername, MiddlewarePassword)
	request.Header.Add("Content-Type", "application/json")

	// Receive core response
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

func transferMoneyOtherBank(w http.ResponseWriter, r *http.Request) {
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

	transferMoney := &TransferMoney{}
	json.NewDecoder(r.Body).Decode(transferMoney)
	dataValidation := validateData(transferMoney)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Read database
	database := connectDatabase()
	token := &Token{}
	tokenExists := database.Where("device_id = @SenderDeviceID", transferMoney).First(token)
	if tokenExists.Error != nil {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"no token found"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	if transferMoney.MiddlewareToken != token.MiddlewareToken {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"invalid token"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	// Send core request
	data, _ := json.Marshal(&Message{
		Status: true,
		Code:   100,
		Data:   transferMoney,
	})

	request, _ := http.NewRequest("POST", fmt.Sprintf("%s/transfer-money-other-bank", CoreURL), bytes.NewBuffer(data))
	request.SetBasicAuth(MiddlewareUsername, MiddlewarePassword)
	request.Header.Add("Content-Type", "application/json")

	// Receive core response
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
