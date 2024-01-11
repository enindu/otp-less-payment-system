package main

import (
	"net/http"

	"gorm.io/gorm"
)

const (
	MiddlewareUsername string = "middleware"
	MiddlewarePassword string = "password"
)

var server *http.Server

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

// Account struct defines accounts table
type Account struct {
	gorm.Model
	FirstName string  `gorm:"not null;type:string;size:191"`
	LastName  string  `gorm:"not null;type:string;size:191"`
	NIC       string  `gorm:"unique;not null;type:string;size:12"`
	Email     string  `gorm:"unique;not null;type:string;size:191"`
	Phone     string  `gorm:"unique;not null;type:string;size:10"`
	Account   string  `gorm:"unique;not null;type:string;size:12"`
	DeviceID  string  `gorm:"default:null;type:string;size:191"`
	Balance   float32 `gorm:"not null;type:float"`
}

// Transactions struct defines transactions table
type Transaction struct {
	gorm.Model
	UniqueID        string  `json:"unique_id"        gorm:"unique;not null;type:string;size:191"`
	SenderID        uint    `json:"sender_id"        gorm:"not null;type:int"`
	ReceiverAccount string  `json:"receiver_account" gorm:"not null;type:string;size:191"`
	Type            string  `json:"type"             gorm:"not null;type:string;size:191"`
	Amount          float32 `json:"amount"           gorm:"not null;type:float"`
	Description     string  `json:"description"      gorm:"default:null;type:string;size:500"`
}

func main() {
	initiateServer()
}
