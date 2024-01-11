package main

import (
	"log"

	"gorm.io/driver/mysql"
	"gorm.io/gorm"
	"gorm.io/gorm/logger"
)

func connectDatabase() *gorm.DB {
	database, exception := gorm.Open(
		mysql.Open("root:root@tcp(localhost)/payment_system_middleware?charset=utf8mb4&parseTime=True&loc=Local"),
		&gorm.Config{Logger: logger.Default.LogMode(logger.Silent)},
	)
	if exception != nil {
		log.Fatal(exception)
	}

	database.AutoMigrate(&Token{})
	return database
}
