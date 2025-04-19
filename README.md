# Bizee – Backend Engineer Challenge (Laravel 12)


This project is an API built with Laravel 12 and Docker that manages companies and their assignment to registered agents in different US states.

In this Project I Use Testing, Events, Listeners, Helpers, and Notifications handle by Laravel 12 and send it using local mailpit.

Event 1: An Agent is Assig to a new company

Event 2: When the porcentage of the Agents in the State are more than 90% the admin will recieved and email.

---

## Requirements

- Docker
- Git
- Composer
- Postman

---

## How to run the Project
from your terminal, powershell or gitbash run the next commands one by one

### 1. Git clone the project
- git clone git@github.com:archisss/bizee-challenge.git
- cd bizee-challenge
- docker-compose up -d --build

### 2. When the Docker is build, lets setup laravel in Docker 
- docker exec -it bizee-app bash

- composer install 
- php artisan key:generate
- php artisan migrate:fresh --seed
- exit 

### 3. Runing Test in Docker 
- docker exec -it bizee-app php artisan test --testsuite=Feature

### Overview of the Project Setup 

Database **bizee** running and tables with informacion already seed 5 users, 5 companies, 51 Agents
EndPoints.

#### Create Company 

- Creates a new company by the user_id and determine if will used a registered agent **registered_agent_id** for the database **true** or if the new company will registered to the same user_id **false**
- In case the **registered_agent_id** is **true** this trigered the Event 1 and an email is send to the email of the registered agent so he/she knows is already assigned a new company.
- In Case the _porcentage_ of agents in the state is over 90 this trigered the Event 2 and an email is send to the admin inform that 90% of the state capacity is reached.

```
[POST] http://localhost:8000/api/companies

Body: 
{
  "user_id": 1,
  "name": "Texas Corp 1",
  "state": "Texas",
  "use_registered_agent_service": true
}

Response:
{
    "user_id": 1,
    "name": "California Corp 1",
    "state": "California",
    "registered_agent_type": "registered_agent",
    "registered_agent_id": 5,
    "updated_at": "2025-04-19T06:14:23.000000Z",
    "created_at": "2025-04-19T06:14:23.000000Z",
    "id": 13
}
```

#### Update Agent Registered in a company for User 

- Get the information of the current Agent assigned to the company in case use_registered_agent_service **true**
- Update the information the registered_agent_id for the user_id in case use_registered_agent_service **false** 

```
[PUT] http://localhost:8000/api/companies/{company_id}

Body: 
{
  "user_id": 1,
  "use_registered_agent_service": true
}

Response:
{
    "id": 1,
    "user_id": 1,
    "name": "Raynor, Cole and Yost",
    "state": "New Jersey",
    "registered_agent_type": "registered_agent",
    "registered_agent_id": 30,
    "created_at": "2025-04-19T05:42:39.000000Z",
    "updated_at": "2025-04-19T06:23:19.000000Z"
}
```
#### Available Agents by State 

- Get the current used capacity in the State **state_name**
  the capacity is calculated by the number of available agents in the state and the assigned companies to each one

```
[GET] http://localhost:8000/api/agent-availability/{State_name}

Body: {}

Response:
{
    "available": false,
    "used_capacity_percent": 100
}
```


## Setup links

**[API]** [localhost:8000](http://localhost:8000) 

**[MailPit]** [localhost:8025](http://localhost:8025) 

**[Bizee Database]** [localhost:8080](http://localhost:8080) 

| User  | Password |
| ------------- |:-------------:|
| user      | secret     |

