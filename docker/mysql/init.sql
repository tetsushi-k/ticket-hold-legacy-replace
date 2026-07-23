-- ticket-hold legacy seed
CREATE TABLE IF NOT EXISTS events (
  id INT PRIMARY KEY,
  title VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS seat_rows (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_id INT NOT NULL,
  seat_no VARCHAR(16) NOT NULL,
  -- intentional debt: free-form status strings (hold / OK / sold / free)
  status VARCHAR(32) NOT NULL DEFAULT 'free',
  buyer VARCHAR(64) NULL,
  hold_until DATETIME NULL,
  UNIQUE KEY uq_event_seat (event_id, seat_no)
);

INSERT INTO events (id, title) VALUES
  (1, 'Demo Live 2026')
ON DUPLICATE KEY UPDATE title = VALUES(title);

INSERT INTO seat_rows (event_id, seat_no, status, buyer, hold_until) VALUES
  (1, 'A-1', 'free', NULL, NULL),
  (1, 'A-2', 'free', NULL, NULL),
  (1, 'A-3', 'free', NULL, NULL),
  (1, 'B-1', 'free', NULL, NULL),
  (1, 'B-2', 'free', NULL, NULL)
ON DUPLICATE KEY UPDATE seat_no = VALUES(seat_no);
