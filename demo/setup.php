<?php

require_once('includes/dbfns.php');

global	$hostName ;
global	$userName ;
global	$password ;
global	$database ;
$connect=connect($hostName,$userName,$password,$database);

$q = "CREATE DATABASE IF NOT EXISTS `".$database."`";
$res=query($connect,$q);
if($res)
	{
		$q="USE `".$database."`";
		$res=query($connect,$q);
		if($res)
			{
				$q=" CREATE TABLE IF NOT EXISTS `nimda` (
					  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
					  `nimda` varchar(30) NOT NULL,
					  `passwd` varchar(40) NOT NULL,
					  PRIMARY KEY (`id`)
				  ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ";
				$res=query($connect,$q);
				if($res)
					{
						$q="INSERT INTO `nimda` (`id`, `nimda`, `passwd`) VALUES (1, 'admin', '9d4e1e23bd5b727046a9e3b4b7db57bd8d6ee684')";	
							$res=query($connect,$q);
							if($res)
								{
									$q="CREATE TABLE IF NOT EXISTS `options` (
										  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
										  `q_id` int(11) NOT NULL,
										  `option` varchar(400) NOT NULL,
										  `votesNumber` varchar(12) NOT NULL,
										  PRIMARY KEY (`id`)
									) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
									$res=query($connect,$q);
									if($res)
										{
											$q="CREATE TABLE IF NOT EXISTS `params` (
												  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
												  `name` varchar(50) NOT NULL,
												  `value` varchar(7) NOT NULL,
												  PRIMARY KEY (`id`)
												) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ";	
											$res=query($connect,$q);
											if($res)
												{
												$q="INSERT INTO `params` (`id`, `name`, `value`) VALUES
												(1, 'lang', 'en')";
												$res=query($connect,$q);
												if($res)
													{
													$q="CREATE TABLE IF NOT EXISTS `questions` (
														  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
														  `question` text NOT NULL,
														  `qDate` datetime NOT NULL,
													  	  `area` varchar(50) NOT NULL,
													  	  `shown` enum('y','n') NOT NULL,
													  	  `multiple_vote` enum('0','1') NOT NULL,
													  	  `exclusiveTo` char(2) NOT NULL,
													  	  `start_on` datetime NOT NULL,
													  	  `expire_on` datetime NOT NULL,
														  PRIMARY KEY (`id`)
														) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";	
													$res=query($connect,$q);
													if($res)
														{
														$q="CREATE TABLE IF NOT EXISTS `votes` (
															  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
															  `q_id` int(10) unsigned NOT NULL,
															  `o_id` int(10) unsigned NOT NULL,
															  `ip` varchar(15) NOT NULL,
															  `country` varchar(64) NOT NULL,
															  `countryCode` varchar(2) NOT NULL,
															  `city` varchar(64) NOT NULL,
															  `voteDate` datetime NOT NULL,
															  UNIQUE KEY `id` (`id`),
															  KEY `countryCode` (`countryCode`),
															  FULLTEXT KEY `ip` (`ip`)
															) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ";	
														$res=query($connect,$q);
														if($res){
																$q="CREATE TABLE IF NOT EXISTS `areas` (
																	  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
																	  `name` varchar(50) NOT NULL,
																	  `radioStyle` tinyint(1) NOT NULL,
																	  `background` varchar(10) NOT NULL,
																	  `forground` varchar(10) NOT NULL,
																	  `bar` varchar(10) NOT NULL,
																	  `width` smallint(6) NOT NULL,
																	  `preview` enum('1','2') NOT NULL,
																	  `expire` int(11) NOT NULL,
																	  PRIMARY KEY (`id`)
																	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
																$res=query($connect,$q);
																if($res){
																	echo 'Congratulation !,The poll has complete the data base installation.';
																	close($connect);exit;
																}else{echo mysqli_error($connect);};
															}else{echo mysqli_error($connect);};
														}else{echo mysqli_error($connect);};
													}else{echo mysqli_error($connect);};
												}	else{echo mysqli_error($connect);};
										}else{echo mysqli_error($connect);};
								}else{echo mysqli_error($connect);};
					}else{echo mysqli_error($connect);};
			}else{echo mysqli_error($connect);};
	}else{echo mysqli_error($connect);};
?>