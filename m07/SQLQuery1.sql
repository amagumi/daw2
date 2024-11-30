CREATE PROCEDURE proc_road_segments_xml
AS
BEGIN
SELECT *
FROM road_segments FOR XML PATH, root('roads');
END

drop table road_segments

CREATE TABLE road_segments (
    segment_id INT PRIMARY KEY,
    road_name NVARCHAR(255) NOT NULL,
    length FLOAT NOT NULL,
    start_latitude FLOAT,
    start_longitude FLOAT,
    end_latitude FLOAT,
    end_longitude FLOAT,
    speed_limit INT
);

drop table road_segments

create database db_1
drop database db_1

-- Caso 1: Segmento de carretera en una autopista
INSERT INTO road_segments (segment_id, road_name, length, start_latitude, start_longitude, end_latitude, end_longitude, speed_limit)
VALUES 
(1, 'Highway 101', 5.2, 34.0522, -118.2437, 34.0622, -118.2537, 65);

-- Caso 2: Segmento de carretera en una zona urbana
INSERT INTO road_segments (segment_id, road_name, length, start_latitude, start_longitude, end_latitude, end_longitude, speed_limit)
VALUES 
(2, 'Main Street', 2.3, 40.7128, -74.0060, 40.7138, -74.0050, 35);

-- Caso 3: Segmento de carretera en una zona rural
INSERT INTO road_segments (segment_id, road_name, length, start_latitude, start_longitude, end_latitude, end_longitude, speed_limit)
VALUES 
(3, 'Country Road', 8.5, 36.7783, -119.4179, 36.7883, -119.4279, 50);
