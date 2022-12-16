<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181226145923 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE Attachment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE photo_report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE instance (id SERIAL NOT NULL, parent_instance_id INT DEFAULT NULL, category_id INT DEFAULT NULL, code VARCHAR(150) NOT NULL, title VARCHAR(255) NOT NULL, domain VARCHAR(255) DEFAULT NULL, site_name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4230B1DE77153098 ON instance (code)');
        $this->addSql('CREATE INDEX IDX_4230B1DE517D606D ON instance (parent_instance_id)');
        $this->addSql('CREATE INDEX IDX_4230B1DE12469DE2 ON instance (category_id)');
        $this->addSql('CREATE TABLE menu_node (id SERIAL NOT NULL, parent_id INT DEFAULT NULL, menu_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(150) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, route VARCHAR(500) DEFAULT NULL, node_order INT DEFAULT 0 NOT NULL, before_text TEXT DEFAULT NULL, after_text TEXT DEFAULT NULL, is_separator BOOLEAN DEFAULT \'false\', is_main BOOLEAN DEFAULT \'false\', is_hide_childs BOOLEAN DEFAULT \'false\', is_hidden BOOLEAN DEFAULT \'false\', is_link_on_id BOOLEAN DEFAULT \'false\', is_published BOOLEAN DEFAULT \'true\', is_deleted BOOLEAN DEFAULT \'false\', old_id INT DEFAULT NULL, manual_updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, manual_created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_search_indexed BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4D30180B727ACA70 ON menu_node (parent_id)');
        $this->addSql('CREATE INDEX IDX_4D30180BCCD7E912 ON menu_node (menu_id)');
        $this->addSql('CREATE TABLE article (id INT NOT NULL, menu_node_id INT DEFAULT NULL, photo_report_id INT DEFAULT NULL, title VARCHAR(1000) NOT NULL, slug VARCHAR(150) NOT NULL, subtitle VARCHAR(255) DEFAULT NULL, content TEXT DEFAULT NULL, author_id INT NOT NULL, category_id INT NOT NULL, is_published BOOLEAN DEFAULT \'true\', is_hot BOOLEAN DEFAULT \'false\', is_important BOOLEAN DEFAULT \'false\', is_social_enabled BOOLEAN DEFAULT \'false\', published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, views_counter INT DEFAULT 0 NOT NULL, manual_views_counter INT DEFAULT 0 NOT NULL, original_article_id INT DEFAULT NULL, old_id INT DEFAULT NULL, original_instance_code VARCHAR(50) DEFAULT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, original_file_path VARCHAR(1000) DEFAULT NULL, related VARCHAR(1000) DEFAULT NULL, show_in_slider BOOLEAN DEFAULT NULL, is_deleted BOOLEAN DEFAULT NULL, is_search_indexed BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_23A0E66CED68269 ON article (menu_node_id)');
        $this->addSql('CREATE INDEX IDX_23A0E66462CF2E1 ON article (photo_report_id)');
        $this->addSql('CREATE TABLE Attachment (id INT NOT NULL, preview_file_url VARCHAR(256) DEFAULT NULL, preview VARCHAR(255) DEFAULT NULL, original_file_name VARCHAR(500) DEFAULT NULL, file_type VARCHAR(255) DEFAULT NULL, file_description TEXT DEFAULT \'\', file_size INT DEFAULT 0, file_update_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE video_report_attachment (id INT NOT NULL, video_report_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E963FA5189ADD955 ON video_report_attachment (video_report_id)');
        $this->addSql('CREATE TABLE widget2panel (id SERIAL NOT NULL, widget_id INT DEFAULT NULL, panel_id INT DEFAULT NULL, title VARCHAR(1000) DEFAULT \'\' NOT NULL, widget_order INT DEFAULT 0, is_published BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4D81969DFBE885E2 ON widget2panel (widget_id)');
        $this->addSql('CREATE INDEX IDX_4D81969D6F6FCB26 ON widget2panel (panel_id)');
        $this->addSql('CREATE TABLE feedback_form_value (id SERIAL NOT NULL, form_id INT DEFAULT NULL, category_id INT DEFAULT NULL, address_group INT DEFAULT NULL, sex_group INT DEFAULT NULL, age_group INT DEFAULT NULL, social_group INT DEFAULT NULL, privilege_group INT DEFAULT NULL, subject VARCHAR(1000) DEFAULT NULL, message TEXT DEFAULT NULL, author VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(1000) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C23DD2AD5FF69B7D ON feedback_form_value (form_id)');
        $this->addSql('CREATE INDEX IDX_C23DD2AD12469DE2 ON feedback_form_value (category_id)');
        $this->addSql('CREATE INDEX IDX_C23DD2AD5A6533E5 ON feedback_form_value (address_group)');
        $this->addSql('CREATE INDEX IDX_C23DD2AD3E91D2D7 ON feedback_form_value (sex_group)');
        $this->addSql('CREATE INDEX IDX_C23DD2ADF88B4253 ON feedback_form_value (age_group)');
        $this->addSql('CREATE INDEX IDX_C23DD2AD2750F057 ON feedback_form_value (social_group)');
        $this->addSql('CREATE INDEX IDX_C23DD2AD3BB311B3 ON feedback_form_value (privilege_group)');
        $this->addSql('CREATE TABLE article_attachment (id INT NOT NULL, article_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4586083A7294869C ON article_attachment (article_id)');
        $this->addSql('CREATE TABLE photo_report_attachment (id INT NOT NULL, photo_report_id INT DEFAULT NULL, file_order INT DEFAULT 0, is_deleted BOOLEAN DEFAULT \'false\', PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C8CCF71D462CF2E1 ON photo_report_attachment (photo_report_id)');
        $this->addSql('CREATE TABLE article_media_attachment (id INT NOT NULL, article_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_642E6C5C7294869C ON article_media_attachment (article_id)');
        $this->addSql('CREATE TABLE widget (id SERIAL NOT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, author_id INT NOT NULL, description VARCHAR(255) DEFAULT NULL, is_published BOOLEAN DEFAULT \'true\' NOT NULL, views_widget_type INT DEFAULT 1 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE feedback_category (id SERIAL NOT NULL, label VARCHAR(255) NOT NULL, code VARCHAR(150) DEFAULT NULL, is_published BOOLEAN DEFAULT \'true\', code_group VARCHAR(255) DEFAULT \'feedback_group\', sort_order_group INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE feedback_form_attachment (id SERIAL NOT NULL, feedback_form_value_id INT DEFAULT NULL, reference VARCHAR(2048) DEFAULT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, preview VARCHAR(255) DEFAULT NULL, original_file_name VARCHAR(500) DEFAULT NULL, file_type VARCHAR(255) DEFAULT NULL, file_description TEXT DEFAULT \'\', file_size INT DEFAULT 0, file_update_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, preview_file_url VARCHAR(256) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5C001B435C87E04D ON feedback_form_attachment (feedback_form_value_id)');
        $this->addSql('CREATE TABLE photo_report (id INT NOT NULL, menu_node_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_published BOOLEAN DEFAULT NULL, views_counter INT DEFAULT 0 NOT NULL, is_deleted BOOLEAN DEFAULT NULL, author_id INT DEFAULT NULL, is_search_indexed BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_919000ACCED68269 ON photo_report (menu_node_id)');
        $this->addSql('CREATE TABLE video_report (id SERIAL NOT NULL, menu_node_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_published BOOLEAN DEFAULT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, views_counter INT DEFAULT 0 NOT NULL, author_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FC78D749CED68269 ON video_report (menu_node_id)');
        $this->addSql('CREATE TABLE system_mode (id SERIAL NOT NULL, code VARCHAR(255) NOT NULL, notification_message VARCHAR(255) DEFAULT NULL, mode_message VARCHAR(255) DEFAULT NULL, is_active_notification BOOLEAN DEFAULT \'false\', is_active_mode BOOLEAN DEFAULT \'false\', PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE article_subscribe (id SERIAL NOT NULL, instance_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, uid VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B28C0F1A3A51721D ON article_subscribe (instance_id)');
        $this->addSql('CREATE UNIQUE INDEX article_subscribe_email_instance_id_key ON article_subscribe (email, instance_id)');
        $this->addSql('CREATE TABLE instance_category (id SERIAL NOT NULL, slug VARCHAR(150) NOT NULL, title VARCHAR(255) NOT NULL, sort_order INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE widget_panel (id SERIAL NOT NULL, label VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE menu (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, code VARCHAR(150) NOT NULL, author_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE feedback_form (id SERIAL NOT NULL, menu_node_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(150) NOT NULL, author_id INT DEFAULT NULL, sort_option VARCHAR(200) DEFAULT \'NULL\' NOT NULL, esia_fields VARCHAR(200) DEFAULT NULL, visible_option VARCHAR(200) DEFAULT \'\' NOT NULL, email_responsible VARCHAR(150) DEFAULT \'\', message_success VARCHAR(1000) DEFAULT \'Ваше сообщение успешно отправлено.\' NOT NULL, message_error VARCHAR(1000) DEFAULT \'При отправке сообщения произошла ошибка.\' NOT NULL, is_published BOOLEAN DEFAULT NULL, is_registered_user BOOLEAN DEFAULT \'false\', is_agree_personal_data BOOLEAN DEFAULT \'true\', description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_972A1CC6CED68269 ON feedback_form (menu_node_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_972A1CC62B36786B ON feedback_form (title)');
        $this->addSql('CREATE TABLE widget_param (id SERIAL NOT NULL, widget2panel_id INT DEFAULT NULL, param_name VARCHAR(100) NOT NULL, param_value TEXT NOT NULL, param_title VARCHAR(255) DEFAULT NULL, param_type INT DEFAULT 0 NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FFBC259F2E2E70F4 ON widget_param (widget2panel_id)');
        $this->addSql('CREATE TABLE portal_access_log (id SERIAL NOT NULL, user_id INT DEFAULT NULL, date DATE NOT NULL, ip_address VARCHAR(255) DEFAULT NULL, client VARCHAR(255) DEFAULT NULL, uri VARCHAR(1000) DEFAULT NULL, controller VARCHAR(255) DEFAULT NULL, method VARCHAR(255) DEFAULT NULL, request_data TEXT NOT NULL, response_code INT NOT NULL, message VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE entity_log (id SERIAL NOT NULL, message TEXT DEFAULT NULL, entity_type VARCHAR(50) DEFAULT NULL, entity_id INT DEFAULT NULL, user_id INT DEFAULT NULL, action_type VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, instance_code VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_role2user (id SERIAL NOT NULL, user_id INT DEFAULT NULL, role_id INT DEFAULT NULL, instance_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_526E9AC5A76ED395 ON user_role2user (user_id)');
        $this->addSql('CREATE INDEX IDX_526E9AC5D60322AC ON user_role2user (role_id)');
        $this->addSql('CREATE INDEX IDX_526E9AC53A51721D ON user_role2user (instance_id)');
        $this->addSql('CREATE TABLE user_permission (id SERIAL NOT NULL, code VARCHAR(150) DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, is_system BOOLEAN DEFAULT \'false\', PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_role (id SERIAL NOT NULL, code VARCHAR(150) DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE portal_user (id SERIAL NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled BOOLEAN NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles TEXT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, middle_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, home_phone VARCHAR(255) DEFAULT NULL, address_registered VARCHAR(512) DEFAULT NULL, address_actual VARCHAR(512) DEFAULT NULL, esia_id VARCHAR(255) DEFAULT NULL, esia_access_token VARCHAR(2000) DEFAULT NULL, esia_refresh_token VARCHAR(64) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_76511E492FC23A8 ON portal_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_76511E4A0D96FBF ON portal_user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_76511E4C05FB297 ON portal_user (confirmation_token)');
        $this->addSql('COMMENT ON COLUMN portal_user.roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE user_role2permission (id SERIAL NOT NULL, role_id INT DEFAULT NULL, permission_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E6E14F36D60322AC ON user_role2permission (role_id)');
        $this->addSql('CREATE INDEX IDX_E6E14F36FED90CCA ON user_role2permission (permission_id)');
        $this->addSql('ALTER TABLE instance ADD CONSTRAINT FK_4230B1DE517D606D FOREIGN KEY (parent_instance_id) REFERENCES instance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE instance ADD CONSTRAINT FK_4230B1DE12469DE2 FOREIGN KEY (category_id) REFERENCES instance_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE menu_node ADD CONSTRAINT FK_4D30180B727ACA70 FOREIGN KEY (parent_id) REFERENCES menu_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE menu_node ADD CONSTRAINT FK_4D30180BCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66CED68269 FOREIGN KEY (menu_node_id) REFERENCES menu_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66462CF2E1 FOREIGN KEY (photo_report_id) REFERENCES photo_report (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_report_attachment ADD CONSTRAINT FK_E963FA5189ADD955 FOREIGN KEY (video_report_id) REFERENCES video_report (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_report_attachment ADD CONSTRAINT FK_E963FA51BF396750 FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE widget2panel ADD CONSTRAINT FK_4D81969DFBE885E2 FOREIGN KEY (widget_id) REFERENCES widget (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE widget2panel ADD CONSTRAINT FK_4D81969D6F6FCB26 FOREIGN KEY (panel_id) REFERENCES widget_panel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_form_value ADD CONSTRAINT FK_C23DD2AD5FF69B7D FOREIGN KEY (form_id) REFERENCES feedback_form (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_form_value ADD CONSTRAINT FK_C23DD2AD12469DE2 FOREIGN KEY (category_id) REFERENCES feedback_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_form_value ADD CONSTRAINT FK_C23DD2AD5A6533E5 FOREIGN KEY (address_group) REFERENCES feedback_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_form_value ADD CONSTRAINT FK_C23DD2AD3E91D2D7 FOREIGN KEY (sex_group) REFERENCES feedback_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_form_value ADD CONSTRAINT FK_C23DD2ADF88B4253 FOREIGN KEY (age_group) REFERENCES feedback_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_form_value ADD CONSTRAINT FK_C23DD2AD2750F057 FOREIGN KEY (social_group) REFERENCES feedback_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_form_value ADD CONSTRAINT FK_C23DD2AD3BB311B3 FOREIGN KEY (privilege_group) REFERENCES feedback_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_attachment ADD CONSTRAINT FK_4586083A7294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_attachment ADD CONSTRAINT FK_4586083ABF396750 FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photo_report_attachment ADD CONSTRAINT FK_C8CCF71D462CF2E1 FOREIGN KEY (photo_report_id) REFERENCES photo_report (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photo_report_attachment ADD CONSTRAINT FK_C8CCF71DBF396750 FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_media_attachment ADD CONSTRAINT FK_642E6C5C7294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_media_attachment ADD CONSTRAINT FK_642E6C5CBF396750 FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_form_attachment ADD CONSTRAINT FK_5C001B435C87E04D FOREIGN KEY (feedback_form_value_id) REFERENCES feedback_form_value (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photo_report ADD CONSTRAINT FK_919000ACCED68269 FOREIGN KEY (menu_node_id) REFERENCES menu_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_report ADD CONSTRAINT FK_FC78D749CED68269 FOREIGN KEY (menu_node_id) REFERENCES menu_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_subscribe ADD CONSTRAINT FK_B28C0F1A3A51721D FOREIGN KEY (instance_id) REFERENCES instance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE feedback_form ADD CONSTRAINT FK_972A1CC6CED68269 FOREIGN KEY (menu_node_id) REFERENCES menu_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE widget_param ADD CONSTRAINT FK_FFBC259F2E2E70F4 FOREIGN KEY (widget2panel_id) REFERENCES widget2panel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_role2user ADD CONSTRAINT FK_526E9AC5A76ED395 FOREIGN KEY (user_id) REFERENCES portal_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_role2user ADD CONSTRAINT FK_526E9AC5D60322AC FOREIGN KEY (role_id) REFERENCES user_role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_role2user ADD CONSTRAINT FK_526E9AC53A51721D FOREIGN KEY (instance_id) REFERENCES instance (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_role2permission ADD CONSTRAINT FK_E6E14F36D60322AC FOREIGN KEY (role_id) REFERENCES user_role (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_role2permission ADD CONSTRAINT FK_E6E14F36FED90CCA FOREIGN KEY (permission_id) REFERENCES user_permission (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE instance DROP CONSTRAINT FK_4230B1DE517D606D');
        $this->addSql('ALTER TABLE article_subscribe DROP CONSTRAINT FK_B28C0F1A3A51721D');
        $this->addSql('ALTER TABLE user_role2user DROP CONSTRAINT FK_526E9AC53A51721D');
        $this->addSql('ALTER TABLE menu_node DROP CONSTRAINT FK_4D30180B727ACA70');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E66CED68269');
        $this->addSql('ALTER TABLE photo_report DROP CONSTRAINT FK_919000ACCED68269');
        $this->addSql('ALTER TABLE video_report DROP CONSTRAINT FK_FC78D749CED68269');
        $this->addSql('ALTER TABLE feedback_form DROP CONSTRAINT FK_972A1CC6CED68269');
        $this->addSql('ALTER TABLE article_attachment DROP CONSTRAINT FK_4586083A7294869C');
        $this->addSql('ALTER TABLE article_media_attachment DROP CONSTRAINT FK_642E6C5C7294869C');
        $this->addSql('ALTER TABLE video_report_attachment DROP CONSTRAINT FK_E963FA51BF396750');
        $this->addSql('ALTER TABLE article_attachment DROP CONSTRAINT FK_4586083ABF396750');
        $this->addSql('ALTER TABLE photo_report_attachment DROP CONSTRAINT FK_C8CCF71DBF396750');
        $this->addSql('ALTER TABLE article_media_attachment DROP CONSTRAINT FK_642E6C5CBF396750');
        $this->addSql('ALTER TABLE widget_param DROP CONSTRAINT FK_FFBC259F2E2E70F4');
        $this->addSql('ALTER TABLE feedback_form_attachment DROP CONSTRAINT FK_5C001B435C87E04D');
        $this->addSql('ALTER TABLE widget2panel DROP CONSTRAINT FK_4D81969DFBE885E2');
        $this->addSql('ALTER TABLE feedback_form_value DROP CONSTRAINT FK_C23DD2AD12469DE2');
        $this->addSql('ALTER TABLE feedback_form_value DROP CONSTRAINT FK_C23DD2AD5A6533E5');
        $this->addSql('ALTER TABLE feedback_form_value DROP CONSTRAINT FK_C23DD2AD3E91D2D7');
        $this->addSql('ALTER TABLE feedback_form_value DROP CONSTRAINT FK_C23DD2ADF88B4253');
        $this->addSql('ALTER TABLE feedback_form_value DROP CONSTRAINT FK_C23DD2AD2750F057');
        $this->addSql('ALTER TABLE feedback_form_value DROP CONSTRAINT FK_C23DD2AD3BB311B3');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E66462CF2E1');
        $this->addSql('ALTER TABLE photo_report_attachment DROP CONSTRAINT FK_C8CCF71D462CF2E1');
        $this->addSql('ALTER TABLE video_report_attachment DROP CONSTRAINT FK_E963FA5189ADD955');
        $this->addSql('ALTER TABLE instance DROP CONSTRAINT FK_4230B1DE12469DE2');
        $this->addSql('ALTER TABLE widget2panel DROP CONSTRAINT FK_4D81969D6F6FCB26');
        $this->addSql('ALTER TABLE menu_node DROP CONSTRAINT FK_4D30180BCCD7E912');
        $this->addSql('ALTER TABLE feedback_form_value DROP CONSTRAINT FK_C23DD2AD5FF69B7D');
        $this->addSql('ALTER TABLE user_role2permission DROP CONSTRAINT FK_E6E14F36FED90CCA');
        $this->addSql('ALTER TABLE user_role2user DROP CONSTRAINT FK_526E9AC5D60322AC');
        $this->addSql('ALTER TABLE user_role2permission DROP CONSTRAINT FK_E6E14F36D60322AC');
        $this->addSql('ALTER TABLE user_role2user DROP CONSTRAINT FK_526E9AC5A76ED395');
        $this->addSql('DROP SEQUENCE article_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE Attachment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE photo_report_id_seq CASCADE');
        $this->addSql('DROP TABLE instance');
        $this->addSql('DROP TABLE menu_node');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE Attachment');
        $this->addSql('DROP TABLE video_report_attachment');
        $this->addSql('DROP TABLE widget2panel');
        $this->addSql('DROP TABLE feedback_form_value');
        $this->addSql('DROP TABLE article_attachment');
        $this->addSql('DROP TABLE photo_report_attachment');
        $this->addSql('DROP TABLE article_media_attachment');
        $this->addSql('DROP TABLE widget');
        $this->addSql('DROP TABLE feedback_category');
        $this->addSql('DROP TABLE feedback_form_attachment');
        $this->addSql('DROP TABLE photo_report');
        $this->addSql('DROP TABLE video_report');
        $this->addSql('DROP TABLE system_mode');
        $this->addSql('DROP TABLE article_subscribe');
        $this->addSql('DROP TABLE instance_category');
        $this->addSql('DROP TABLE widget_panel');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE feedback_form');
        $this->addSql('DROP TABLE widget_param');
        $this->addSql('DROP TABLE portal_access_log');
        $this->addSql('DROP TABLE entity_log');
        $this->addSql('DROP TABLE user_role2user');
        $this->addSql('DROP TABLE user_permission');
        $this->addSql('DROP TABLE user_role');
        $this->addSql('DROP TABLE portal_user');
        $this->addSql('DROP TABLE user_role2permission');
    }
}
