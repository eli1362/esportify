# Esportify

Esportify is an online platform for esports events and tournaments. Players can register for tournaments, participate in discussions, and track their performance. Organizers can create and manage events, while admins validate and moderate the events.

## Table of Contents
1. [Project Description](#project-description)
2. [Features](#features)
3. [Technologies Used](#technologies-used)
4. [Installation Instructions](#installation-instructions)
5. [Usage](#usage)
6. [Contributing](#contributing)
7. [License](#license)

## Project Description
Esportify is a web platform for esports competitions. It allows players to register for upcoming events, participate in games, and track their progress.

## Features
- **Player functionality**: Players can register for tournaments, join discussions, and view their past performance.
- **Organizer functionality**: Organizers can create and manage events, as well as manage player registrations.
- **Admin functionality**: Admins validate events, moderate user activity, and manage platform settings.

## Technologies Used
- **Frontend**: HTML5, CSS3 (Bootstrap), JavaScript
- **Backend**: PHP (PDO for database interaction)
- **Database**: MySQL/MariaDB
- **NoSQL**: MongoDB (for some non-relational data)
- **Deployment**: Heroku, AWS, DigitalOcean

## Installation Instructions
1. Clone the repository:
   ```bash
   git clone https://github.com/eli1362/esportify.git
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Set up the database and import the schema from `database/schema.sql`.
4. Run the local PHP server:
   ```bash
   php -S localhost:8000
   ```

## License
This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
