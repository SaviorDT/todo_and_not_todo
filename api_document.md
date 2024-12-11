# 環境
1. 請記得在 header 加入 "accept": "application/json"，否則回傳的東西可能會變成網頁
2. 與權限有關的 api 需要先登入，然後在 header 加入 "Authorization": "Bearer `{access_token}`"

# 帳號相關
## `/api/auth/register` `POST`
```php
'name' => 'required|string|max:255',
'email' => 'required|string|max:255|unique:users',
'password' => 'required|string|max:255'
```
### return {user}

## `/api/auth/login` `POST`
```php
'email' => 'required|string|max:255',
'password' => 'required|string|max:255'
```
### return {user, access_token, refresh_token}

## `/api/auth/refresh` `POST`
```php
'refresh_token' => 'required|string|max:1024'
```
### return {access_token, refresh_token}
<br>

# Todo
## `/api/todo` `POST`
```php
'title' => 'required|string|max:255',
'description' => 'required|string|max:10240',
'start_date' => 'date',
'due_date' => 'date'
```
### return {created_todo}

## `/api/todo` `GET`
### retrun [todos]

## `/api/todo/{id}` `PATCH`
```php
'title' => 'string|max:255',
'description' => 'string|max:10240',
'start_date' => 'date',
'due_date' => 'date',
'completed' => 'boolean',
'id' => 'required|exists:todos'
```
### retrun {updated_todo}
<br>

# Progress
## `/api/progress` `POST`
```php
'title' => 'required|string|max:255',
'description' => 'required|string|max:10240',
'max_value' => 'required|integer|max:2147483647',
'start_date' => 'date',
'due_date' => 'required|date'
```
### return {created_todo}
  
## `/api/progress` `GET`
### retrun [progresses]

## `/api/progress/{id}` `PATCH`
```php
'title' => 'string|max:255',
'description' => 'string|max:10240',
'current_value' => 'integer|max:2147483647',
'max_value' => 'integer|max:2147483647',
'start_date' => 'date',
'due_date' => 'date',
'id' => 'required|exists:progresses'
```
### retrun {updated_progress}