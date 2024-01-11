package main

import (
	"net/http"
)

const (
	ClientUsername         string = "client"
	ClientPassword         string = "password"
	AuthenticationUsername string = "authentication"
	AuthenticationPassword string = "password"
	MiddlewareURL          string = "https://localhost:5001/api"
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

// GenerateKey struct defines generate key data
type GenerateKey struct {
	DeviceID            string `json:"device-id"            validate:"required,max=191"`
	AuthenticationToken string `json:"authentication-token" validate:"max=191"`
}

func main() {
	initiateClient()
	initiateServer()
}
