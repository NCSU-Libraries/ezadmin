--
-- Modify the resource table, adding a last edit date and a last edited by user column
--

ALTER TABLE resource ADD last_edit_date TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP;
ALTER TABLE resource ADD last_edited_by_user VARCHAR(255) NULL;

