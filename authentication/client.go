package main

import (
	"crypto/tls"
	"crypto/x509"
	"fmt"
	"log"
	"net/http"
	"os"
	"time"

	"golang.org/x/net/http2"
)

func initiateClient() {
	client = &http.Client{
		Timeout: 5 * time.Second,
		Transport: &http2.Transport{
			DisableCompression: true,
			AllowHTTP:          false,
			ReadIdleTimeout:    5 * time.Second,
			PingTimeout:        5 * time.Second,
			TLSClientConfig: &tls.Config{
				InsecureSkipVerify: false,
				ServerName:         "localhost",
				RootCAs:            getRootCertificateAuthorities(),
			},
		},
	}
}

func getRootCertificateAuthorities() *x509.CertPool {
	certificates, exception := os.ReadDir("certificates")
	if exception != nil {
		log.Fatal(exception)
	}

	certificatePool := x509.NewCertPool()
	for _, v := range certificates {
		certificate, exception := os.ReadFile(fmt.Sprintf("certificates/%s", v.Name()))
		if exception != nil {
			log.Fatal(exception)
		}

		certificatePool.AppendCertsFromPEM(certificate)
	}

	return certificatePool
}
