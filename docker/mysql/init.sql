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

-- After: 明示状態（legacy の seat_rows とは別テーブル）
CREATE TABLE IF NOT EXISTS after_seat_inventories (
  performance_id VARCHAR(16) NOT NULL,
  seat_no VARCHAR(16) NOT NULL,
  state ENUM('available', 'on_hold', 'confirmed') NOT NULL DEFAULT 'available',
  buyer_id VARCHAR(64) NULL,
  hold_until DATETIME NULL,
  PRIMARY KEY (performance_id, seat_no)
);

INSERT INTO after_seat_inventories (performance_id, seat_no, state, buyer_id, hold_until) VALUES
  ('P1', 'A-1', 'available', NULL, NULL),
  ('P1', 'A-2', 'available', NULL, NULL),
  ('P1', 'A-3', 'on_hold', 'buyer-a', '2020-01-01 00:00:00'),
  ('P1', 'B-1', 'available', NULL, NULL),
  ('P1', 'B-2', 'available', NULL, NULL)
ON DUPLICATE KEY UPDATE seat_no = VALUES(seat_no);
