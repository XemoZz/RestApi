# RestApi
 
## How to launch a project

Install xamp, install composer, put the project folder into the xamp htdocs folder. Go into the project folder using cmd, run composer.phar install. Start apache and my_sql in xampp. Import the database, rest_api.sql file.

# Film Database Management API

## Overview

This API allows users to interact with a film database, enabling operations such as retrieving, adding, updating, and deleting films.

## Endpoints

### GET /films

- **Description**: Retrieves a list of all films in the database.
- **Response**: JSON array of film objects.
- **Example Response**:[{"film_id": 1, "tytul": "Film Title", "gatunek": "Genre", "rok_produkcji": 2020, "ocena": 8.5},
...]

### POST /films

- **Description**: Adds a new film to the database.
- **Request Body**: JSON object containing film details.
- **Required Fields**: `tytul`, `gatunek`, `rok_produkcji`, `ocena`.
- **Example Request**:
{
"tytul": "New Film",
"gatunek": "Drama",
"rok_produkcji": 2021,
"ocena": 7.5
}

- **Response**: Confirmation message.
- **Example Response**:
{"message": "Film dodany pomyślnie"}


### PUT /films/{film_id}

- **Description**: Updates the details of an existing film.
- **URL Parameter**: `film_id` (ID of the film to update).
- **Request Body**: JSON object with fields to update.
- **Example Request**:
{
"tytul": "Updated Title",
"ocena": 9.0
}


- **Response**: Confirmation message.
- **Example Response**:
{"message": "Film zaktualizowany pomyślnie"}



### DELETE /films/{film_id}

- **Description**: Deletes a film from the database.
- **URL Parameter**: `film_id` (ID of the film to delete).
- **Response**: Confirmation message.
- **Example Response**:
{"message": "Film usunięty pomyślnie"}



## Notes

- All endpoints respond with JSON.
- Implement secure handling of database connections and queries.
- Implement error handling for clearer failure messages.
- Consider adding authentication and authorization for production use.

## Example Usage

**Add a new film**:
```bash
curl -X POST http://example.com/films -H "Content-Type: application/json" -d '{"tytul": "Example Film", "gatunek": "Comedy", "rok_produkcji": 2022, "ocena": 8.0}'
