package main

import (
	"net/http"

	"gorm.io/gorm"
)

const (
	ClientUsername         string = "client"
	ClientPassword         string = "password"
	MiddlewareUsername     string = "middleware"
	MiddlewarePassword     string = "password"
	AuthenticationUsername string = "authentication"
	AuthenticationPassword string = "password"
	CoreURL                string = "https://localhost:5002/api"
)

var (
	client *http.Client
	server *http.Server
)

// Message struct defines message data
type Message struct {
	Status bool        `json:"status"`
	Code   int         `json:"code"`
	Data   interface{} `json:"data"`
}

// Register struct defines register data
type Register struct {
	FirstName string `json:"first-name" validate:"required,alpha,max=191"`
	LastName  string `json:"last-name"  validate:"required,alpha,max=191"`
	NIC       string `json:"nic"        validate:"required,alphanum,min=10,max=12"`
	Email     string `json:"email"      validate:"required,email,max=191"`
	Phone     string `json:"phone"      validate:"required,min=10,max=10"`
	Account   string `json:"account"    validate:"required,min=12,max=12"`
}

// GenerateKey struct defines generate key data
type GenerateKey struct {
	DeviceID            string `json:"device-id"            validate:"required,max=191"`
	AuthenticationToken string `json:"authentication-token" validate:"required,max=191"`
}

// GetInformation struct defines get balance data
type GetInformation struct {
	NIC      string `json:"nic"       validate:"required,alphanum,min=10,max=12"`
	Email    string `json:"email"     validate:"required,email,max=191"`
	Phone    string `json:"phone"     validate:"required,min=10,max=10"`
	Account  string `json:"account"   validate:"required,min=12,max=12"`
	DeviceID string `json:"device-id" validate:"required"`
}

// TransferMoney struct defines transfer money data
type TransferMoney struct {
	SenderNIC       string  `json:"sender-nic"       validate:"required,alphanum,min=10,max=12"`
	SenderEmail     string  `json:"sender-email"     validate:"required,email,max=191"`
	SenderPhone     string  `json:"sender-phone"     validate:"required,min=10,max=10"`
	SenderAccount   string  `json:"sender-account"   validate:"required,min=12,max=12"`
	SenderDeviceID  string  `json:"sender-device-id" validate:"required,max=191"`
	ReceiverAccount string  `json:"receiver-account" validate:"required,min=12,max=12"`
	MiddlewareToken string  `json:"middleware-token" validate:"required,max=191"`
	Type            string  `json:"type"             validate:"required,max=191"`
	Amount          float32 `json:"amount"           validate:"required,numeric"`
	Description     string  `json:"description"      validate:"max=500"`
}

// Tokens struct defines tokens table
type Token struct {
	gorm.Model
	DeviceID            string `gorm:"unique;not null;type:string;size:191"`
	AuthenticationToken string `gorm:"not null;type:string;size:191"`
	MiddlewareToken     string `gorm:"not null;type:string;size:191"`
}

func main() {
	initiateClient()
	initiateServer()
}
