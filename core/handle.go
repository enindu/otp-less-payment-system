package main

import (
	"encoding/json"
	"fmt"
	"log"
	"net/http"

	"github.com/rs/xid"
)

func register(w http.ResponseWriter, r *http.Request) {
	// Set response headers
	w.Header().Add("Strict-Transport-Security", "max-age=63072000; includeSubDomains")
	w.Header().Add("Content-Type", "application/json")

	// Receive middleware request
	serverValidation := validateServer(r, MiddlewareUsername, MiddlewarePassword)
	if !serverValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, serverValidation.Data)
		w.WriteHeader(serverValidation.Code)
		json.NewEncoder(w).Encode(serverValidation)
		return
	}

	message := &Message{}
	json.NewDecoder(r.Body).Decode(message)
	data, _ := json.Marshal(message.Data)

	register := &Register{}
	json.Unmarshal(data, register)
	dataValidation := validateData(register)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Read and update database
	database := connectDatabase()
	account := &Account{}
	accountExists := database.Where("nic = @NIC AND email = @Email AND phone = @Phone AND account = @Account", register).First(account)
	if accountExists.Error != nil {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"no account found"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	if account.DeviceID != "" {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"already has device"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	deviceID := xid.New().String()
	database.Model(account).Update("device_id", deviceID)

	// Return middleware response
	message = &Message{
		Status: true,
		Code:   200,
		Data:   []string{deviceID},
	}

	w.WriteHeader(message.Code)
	json.NewEncoder(w).Encode(message)
}

func getBalance(w http.ResponseWriter, r *http.Request) {
	// Set response headers
	w.Header().Add("Strict-Transport-Security", "max-age=63072000; includeSubDomains")
	w.Header().Add("Content-Type", "application/json")

	// Receive middleware request
	serverValidation := validateServer(r, MiddlewareUsername, MiddlewarePassword)
	if !serverValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, serverValidation.Data)
		w.WriteHeader(serverValidation.Code)
		json.NewEncoder(w).Encode(serverValidation)
		return
	}

	message := &Message{}
	json.NewDecoder(r.Body).Decode(message)
	data, _ := json.Marshal(message.Data)

	getInformation := &GetInformation{}
	json.Unmarshal(data, getInformation)
	dataValidation := validateData(getInformation)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Read database
	database := connectDatabase()
	account := &Account{}
	accountExists := database.Where("nic = @NIC AND email = @Email AND phone = @Phone AND account = @Account AND device_id = @DeviceID", getInformation).First(account)
	if accountExists.Error != nil {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"no account found"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	// Return middleware response
	message = &Message{
		Status: true,
		Code:   200,
		Data:   []string{fmt.Sprintf("%f", account.Balance)},
	}

	w.WriteHeader(message.Code)
	json.NewEncoder(w).Encode(message)
}

func getTransactions(w http.ResponseWriter, r *http.Request) {
	// Set response headers
	w.Header().Add("Strict-Transport-Security", "max-age=63072000; includeSubDomains")
	w.Header().Add("Content-Type", "application/json")

	// Receive middleware request
	serverValidation := validateServer(r, MiddlewareUsername, MiddlewarePassword)
	if !serverValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, serverValidation.Data)
		w.WriteHeader(serverValidation.Code)
		json.NewEncoder(w).Encode(serverValidation)
		return
	}

	message := &Message{}
	json.NewDecoder(r.Body).Decode(message)
	data, _ := json.Marshal(message.Data)

	getInformation := &GetInformation{}
	json.Unmarshal(data, getInformation)
	dataValidation := validateData(getInformation)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Read database
	database := connectDatabase()
	account := &Account{}
	accountExists := database.Where("nic = @NIC AND email = @Email AND phone = @Phone AND account = @Account AND device_id = @DeviceID", getInformation).First(account)
	if accountExists.Error != nil {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"no account found"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	transactions := &[]Transaction{}
	database.Where("sender_id = ?", account.ID).Order("created_at desc").Find(transactions)

	// Return middleware response
	message = &Message{
		Status: true,
		Code:   200,
		Data:   transactions,
	}

	w.WriteHeader(message.Code)
	json.NewEncoder(w).Encode(message)
}

func transferMoneySameBank(w http.ResponseWriter, r *http.Request) {
	// Set response headers
	w.Header().Add("Strict-Transport-Security", "max-age=63072000; includeSubDomains")
	w.Header().Add("Content-Type", "application/json")

	// Receive middleware request
	serverValidation := validateServer(r, MiddlewareUsername, MiddlewarePassword)
	if !serverValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, serverValidation.Data)
		w.WriteHeader(serverValidation.Code)
		json.NewEncoder(w).Encode(serverValidation)
		return
	}

	message := &Message{}
	json.NewDecoder(r.Body).Decode(message)
	data, _ := json.Marshal(message.Data)

	transferMoney := &TransferMoney{}
	json.Unmarshal(data, transferMoney)
	dataValidation := validateData(transferMoney)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Read and update database
	database := connectDatabase()
	senderAccount := &Account{}
	senderAccountExists := database.Where("nic = @SenderNIC AND email = @SenderEmail AND phone = @SenderPhone AND account = @SenderAccount", transferMoney).First(senderAccount)
	if senderAccountExists.Error != nil {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"no sender account found"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	receiverAccount := &Account{}
	receiverAccountExists := database.Where("account = @ReceiverAccount", transferMoney).First(receiverAccount)
	if receiverAccountExists.Error != nil {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"no receiver account found"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	if transferMoney.Amount > senderAccount.Balance {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"not enough balance"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	database.Model(senderAccount).Update("balance", senderAccount.Balance-transferMoney.Amount)
	database.Model(receiverAccount).Update("balance", receiverAccount.Balance+transferMoney.Amount)

	uniqueID := xid.New().String()
	database.Create(&Transaction{
		UniqueID:        uniqueID,
		SenderID:        senderAccount.ID,
		ReceiverAccount: transferMoney.ReceiverAccount,
		Type:            transferMoney.Type,
		Amount:          transferMoney.Amount,
		Description:     transferMoney.Description,
	})

	// Return middleware response
	message = &Message{
		Status: true,
		Code:   200,
		Data:   []string{uniqueID},
	}

	w.WriteHeader(message.Code)
	json.NewEncoder(w).Encode(message)
}

func transferMoneyOtherBank(w http.ResponseWriter, r *http.Request) {
	// Set response headers
	w.Header().Add("Strict-Transport-Security", "max-age=63072000; includeSubDomains")
	w.Header().Add("Content-Type", "application/json")

	// Receive middleware request
	serverValidation := validateServer(r, MiddlewareUsername, MiddlewarePassword)
	if !serverValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, serverValidation.Data)
		w.WriteHeader(serverValidation.Code)
		json.NewEncoder(w).Encode(serverValidation)
		return
	}

	message := &Message{}
	json.NewDecoder(r.Body).Decode(message)
	data, _ := json.Marshal(message.Data)

	transferMoney := &TransferMoney{}
	json.Unmarshal(data, transferMoney)
	dataValidation := validateData(transferMoney)
	if !dataValidation.Status {
		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, dataValidation.Data)
		w.WriteHeader(dataValidation.Code)
		json.NewEncoder(w).Encode(dataValidation)
		return
	}

	// Read and update database
	database := connectDatabase()
	senderAccount := &Account{}
	senderAccountExists := database.Where("nic = @SenderNIC AND email = @SenderEmail AND phone = @SenderPhone AND account = @SenderAccount", transferMoney).First(senderAccount)
	if senderAccountExists.Error != nil {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"no sender account found"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	if transferMoney.Amount > senderAccount.Balance {
		message := &Message{
			Status: false,
			Code:   400,
			Data:   []string{"not enough balance"},
		}

		log.Println(r.Method, r.RemoteAddr, r.Proto, r.RequestURI, message.Data)
		w.WriteHeader(message.Code)
		json.NewEncoder(w).Encode(message)
		return
	}

	database.Model(senderAccount).Update("balance", senderAccount.Balance-transferMoney.Amount)

	uniqueID := xid.New().String()
	database.Create(&Transaction{
		UniqueID:        uniqueID,
		SenderID:        senderAccount.ID,
		ReceiverAccount: transferMoney.ReceiverAccount,
		Type:            transferMoney.Type,
		Amount:          transferMoney.Amount,
		Description:     transferMoney.Description,
	})

	// Return middleware response
	message = &Message{
		Status: true,
		Code:   200,
		Data:   []string{uniqueID},
	}

	w.WriteHeader(message.Code)
	json.NewEncoder(w).Encode(message)
}
