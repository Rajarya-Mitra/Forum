create table users (
    u_id int auto_increment primary key,
    username varchar(50) not null unique,
    u_password varchar(255) not null,
    email varchar(100) not null unique,
    created_at timestamp default current_timestamp
);

create table categories (
    c_id int auto_increment primary key,
    c_name varchar(100) not null,
    c_description text,
    created_at timestamp default current_timestamp
);

create table threads (
    t_id int auto_increment primary key,
    c_id int not null references categories,
    u_id int not null references users,
    title varchar(255) not null,
    t_body text not null,
    created_at timestamp default current_timestamp
);

create table posts (
    p_id int auto_increment primary key,
    t_id int not null references threads,
    u_id int not null references users,
    P_body text not null,
    created_at timestamp default current_timestamp
);