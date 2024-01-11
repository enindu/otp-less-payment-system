package main

import (
	"crypto/tls"
	"log"
	"net/http"
	"os"
	"time"
)

func initiateServer() {
	http.HandleFunc("/api/register", register)
	http.HandleFunc("/api/generate-key", generateKey)
	http.HandleFunc("/api/get-balance", getBalance)
	http.HandleFunc("/api/get-transactions", getTransactions)
	http.HandleFunc("/api/transfer-money-same-bank", transferMoneySameBank)
	http.HandleFunc("/api/transfer-money-other-bank", transferMoneyOtherBank)

	certificate, exception := os.ReadFile("certificates/middleware-certificate.pem")
	if exception != nil {
		log.Fatal(exception)
	}

	key, exception := os.ReadFile("keys/middleware-key.pem")
	if exception != nil {
		log.Fatal(exception)
	}

	keyPair, exception := tls.X509KeyPair(certificate, key)
	if exception != nil {
		log.Fatal(exception)
	}

	server = &http.Server{
		Addr:         ":5001",
		ReadTimeout:  5 * time.Second,
		WriteTimeout: 5 * time.Second,
		TLSConfig: &tls.Config{
			Certificates:             []tls.Certificate{keyPair},
			ServerName:               "localhost",
			MinVersion:               tls.VersionTLS12,
			MaxVersion:               tls.VersionTLS13,
			PreferServerCipherSuites: true,
			CipherSuites:             []uint16{tls.TLS_ECDHE_RSA_WITH_AES_128_GCM_SHA256},
			CurvePreferences: []tls.CurveID{
				tls.CurveP256,
				tls.CurveP384,
				tls.CurveP521,
			},
		},
	}

	log.Println("Middleware is running on localhost:5001")
	server.ListenAndServeTLS("", "")
}
