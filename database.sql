CREATE TABLE IF NOT EXISTS `transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_id` text,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description` text,
  `currency` varchar(10) DEFAULT NULL,
  `gateway` varchar(50) DEFAULT NULL,
  `environment` varchar(20) DEFAULT NULL,
  `status` enum('processing','successful','failed') DEFAULT 'processing',
  `response_txt` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;