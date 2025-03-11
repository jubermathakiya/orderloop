# Library Management API Take Home Exercise

This exercise is designed to assess your ability to build a robust, production-ready RESTful API using Laravel. It covers Laravel fundamentals, API design, testing, performance, security, and more. The goal is to demonstrate your skills in building a secure, maintainable, and high-performance API application.

---
## Installation steps 

- Download the code
- Setup the database and username in env
- run command : 
    1) composer update
    2) php artisan migrate:fresh --seed
    3) php artisan l5-swagger:generate
- open the swagger document on follogin path
 base_url/api/documentation
 
## Overview

You are tasked with developing a **Library Management API** that allows users to browse books, borrow/return them, and enables administrators to manage the book catalog. The API should incorporate best practices in authentication, role-based access control, event-driven design, caching, error handling, logging, and automated testing.

