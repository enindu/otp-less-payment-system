package main

import (
	"net/http"

	"gopkg.in/go-playground/validator.v9"
)

func validateServer(r *http.Request, u string, p string) *Message {
	if r.Method != "POST" {
		return &Message{
			Status: false,
			Code:   405,
			Data:   []string{"method is invalid"},
		}
	}

	if r.Header.Get("Content-Type") != "application/json" {
		return &Message{
			Status: false,
			Code:   417,
			Data:   []string{"content type is invalid"},
		}
	}

	username, password, ok := r.BasicAuth()
	if !ok || username != u || password != p {
		return &Message{
			Status: false,
			Code:   511,
			Data:   []string{"basic authentication is invalid"},
		}
	}

	return &Message{
		Status: true,
		Code:   100,
	}
}

func validateData(d interface{}) *Message {
	validation := validator.New().Struct(d)
	if validation != nil {
		errors := make([]string, 0)
		for _, v := range validation.(validator.ValidationErrors) {
			errors = append(errors, v.Field())
		}

		return &Message{
			Status: false,
			Code:   400,
			Data:   errors,
		}
	}

	return &Message{
		Status: true,
		Code:   100,
	}
}
