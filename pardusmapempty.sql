-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 26, 2024 at 10:10 PM
-- Server version: 5.7.23-23
-- PHP Version: 8.1.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ifcpaute_pardusmapempty`
--

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_Buildings`
--

CREATE TABLE `Artemis_Buildings` (
  `id` int(11) NOT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(11) NOT NULL DEFAULT '0',
  `y` tinyint(11) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `image` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `owner` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `alliance` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `stock` tinyint(4) DEFAULT '0',
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `population` int(11) DEFAULT NULL,
  `crime` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `condition` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `freespace` int(11) DEFAULT NULL,
  `credit` bigint(11) DEFAULT NULL,
  `security` int(11) NOT NULL DEFAULT '0',
  `starbase` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `stock_updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_Maps`
--

CREATE TABLE `Artemis_Maps` (
  `id` int(11) NOT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(4) NOT NULL DEFAULT '0',
  `y` tinyint(4) NOT NULL DEFAULT '0',
  `bg` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `fg` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `fg_spotted` datetime DEFAULT NULL,
  `fg_updated` datetime DEFAULT NULL,
  `npc` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `npc_cloaked` tinyint(4) DEFAULT NULL,
  `npc_spotted` datetime DEFAULT NULL,
  `npc_updated` datetime DEFAULT NULL,
  `wormhole` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `starbase` tinyint(4) NOT NULL DEFAULT '0',
  `security` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_Missions`
--

CREATE TABLE `Artemis_Missions` (
  `id` int(11) NOT NULL,
  `loc` int(11) NOT NULL,
  `comp` tinyint(4) NOT NULL,
  `rank` tinyint(4) DEFAULT NULL,
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type_img` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `amount` tinyint(4) DEFAULT NULL,
  `target` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(4) DEFAULT NULL,
  `y` tinyint(4) DEFAULT NULL,
  `time` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_New_Stock`
--

CREATE TABLE `Artemis_New_Stock` (
  `id` int(11) NOT NULL,
  `name` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `bal` int(11) NOT NULL DEFAULT '0',
  `min` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `buy` int(11) NOT NULL DEFAULT '0',
  `sell` int(11) NOT NULL DEFAULT '0',
  `stock` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_Npcs`
--

CREATE TABLE `Artemis_Npcs` (
  `id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `image` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `hull` int(11) DEFAULT NULL,
  `armor` int(11) DEFAULT NULL,
  `shield` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_Personal_Resources`
--

CREATE TABLE `Artemis_Personal_Resources` (
  `id` int(11) NOT NULL,
  `loc` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_Squadrons`
--

CREATE TABLE `Artemis_Squadrons` (
  `id` int(11) NOT NULL,
  `image` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `type` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `weapons` int(11) NOT NULL,
  `credit` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_Stock`
--

CREATE TABLE `Artemis_Stock` (
  `id` int(11) NOT NULL,
  `Food Amount` int(11) DEFAULT '0',
  `Food Bal` int(11) DEFAULT '0',
  `Food Min` int(11) DEFAULT '0',
  `Food Max` int(11) DEFAULT '0',
  `Food Buy` int(11) DEFAULT '0',
  `Food Sell` int(11) DEFAULT '0',
  `Energy Amount` int(11) DEFAULT '0',
  `Energy Bal` int(11) DEFAULT '0',
  `Energy Min` int(11) DEFAULT '0',
  `Energy Max` int(11) DEFAULT '0',
  `Energy Buy` int(11) DEFAULT '0',
  `Energy Sell` int(11) DEFAULT '0',
  `Water Amount` int(11) DEFAULT '0',
  `Water Bal` int(11) DEFAULT '0',
  `Water Min` int(11) DEFAULT '0',
  `Water Max` int(11) DEFAULT '0',
  `Water Buy` int(11) DEFAULT '0',
  `Water Sell` int(11) DEFAULT '0',
  `Animal embryos Amount` int(11) DEFAULT '0',
  `Animal embryos Bal` int(11) DEFAULT '0',
  `Animal embryos Min` int(11) DEFAULT '0',
  `Animal embryos Max` int(11) DEFAULT '0',
  `Animal embryos Buy` int(11) DEFAULT '0',
  `Animal embryos Sell` int(11) DEFAULT '0',
  `Ore Amount` int(11) DEFAULT '0',
  `Ore Bal` int(11) DEFAULT '0',
  `Ore Min` int(11) DEFAULT '0',
  `Ore Max` int(11) DEFAULT '0',
  `Ore Buy` int(11) DEFAULT '0',
  `Ore Sell` int(11) DEFAULT '0',
  `Metal Amount` int(11) DEFAULT '0',
  `Metal Bal` int(11) DEFAULT '0',
  `Metal Min` int(11) DEFAULT '0',
  `Metal Max` int(11) DEFAULT '0',
  `Metal Buy` int(11) DEFAULT '0',
  `Metal Sell` int(11) DEFAULT '0',
  `Electronics Amount` int(11) DEFAULT '0',
  `Electronics Bal` int(11) DEFAULT '0',
  `Electronics Min` int(11) DEFAULT '0',
  `Electronics Max` int(11) DEFAULT '0',
  `Electronics Buy` int(11) DEFAULT '0',
  `Electronics Sell` int(11) DEFAULT '0',
  `Robots Amount` int(11) DEFAULT '0',
  `Robots Bal` int(11) DEFAULT '0',
  `Robots Min` int(11) DEFAULT '0',
  `Robots Max` int(11) DEFAULT '0',
  `Robots Buy` int(11) DEFAULT '0',
  `Robots Sell` int(11) DEFAULT '0',
  `Heavy plastics Amount` int(11) DEFAULT '0',
  `Heavy plastics Bal` int(11) DEFAULT '0',
  `Heavy plastics Min` int(11) DEFAULT '0',
  `Heavy plastics Max` int(11) DEFAULT '0',
  `Heavy plastics Buy` int(11) DEFAULT '0',
  `Heavy plastics Sell` int(11) DEFAULT '0',
  `Hand weapons Amount` int(11) DEFAULT '0',
  `Hand weapons Bal` int(11) DEFAULT '0',
  `Hand weapons Min` int(11) DEFAULT '0',
  `Hand weapons Max` int(11) DEFAULT '0',
  `Hand weapons Buy` int(11) DEFAULT '0',
  `Hand weapons Sell` int(11) DEFAULT '0',
  `Medicines Amount` int(11) DEFAULT '0',
  `Medicines Bal` int(11) DEFAULT '0',
  `Medicines Min` int(11) DEFAULT '0',
  `Medicines Max` int(11) DEFAULT '0',
  `Medicines Buy` int(11) DEFAULT '0',
  `Medicines Sell` int(11) DEFAULT '0',
  `Nebula gas Amount` int(11) DEFAULT '0',
  `Nebula Gas Bal` int(11) DEFAULT '0',
  `Nebula gas Min` int(11) DEFAULT '0',
  `Nebula gas Max` int(11) DEFAULT '0',
  `Nebula gas Buy` int(11) DEFAULT '0',
  `Nebula gas Sell` int(11) DEFAULT '0',
  `Chemical supplies Amount` int(11) DEFAULT '0',
  `Chemical supplies Bal` int(11) DEFAULT '0',
  `Chemical supplies Min` int(11) DEFAULT '0',
  `Chemical supplies Max` int(11) DEFAULT '0',
  `Chemical supplies Buy` int(11) DEFAULT '0',
  `Chemical supplies Sell` int(11) DEFAULT '0',
  `Gem stones Amount` int(11) DEFAULT '0',
  `Gem stones Bal` int(11) DEFAULT '0',
  `Gem stones Min` int(11) DEFAULT '0',
  `Gem stones Max` int(11) DEFAULT '0',
  `Gem stones Buy` int(11) DEFAULT '0',
  `Gem stones Sell` int(11) DEFAULT '0',
  `Liquor Amount` int(11) DEFAULT '0',
  `Liquor Bal` int(11) DEFAULT '0',
  `Liquor Min` int(11) DEFAULT '0',
  `Liquor Max` int(11) DEFAULT '0',
  `Liquor Buy` int(11) DEFAULT '0',
  `Liquor Sell` int(11) DEFAULT '0',
  `Hydrogen fuel Amount` int(11) DEFAULT '0',
  `Hydrogen fuel Bal` int(11) DEFAULT '0',
  `Hydrogen fuel Min` int(11) DEFAULT '0',
  `Hydrogen fuel Max` int(11) DEFAULT '0',
  `Hydrogen fuel Buy` int(11) DEFAULT '0',
  `Hydrogen fuel Sell` int(11) DEFAULT '0',
  `Exotic matter Amount` int(11) DEFAULT '0',
  `Exotic matter Bal` int(11) DEFAULT '0',
  `Exotic matter Min` int(11) DEFAULT '0',
  `Exotic matter Max` int(11) DEFAULT '0',
  `Exotic matter Buy` int(11) DEFAULT '0',
  `Exotic matter Sell` int(11) DEFAULT '0',
  `Optical components Amount` int(11) DEFAULT '0',
  `Optical components Bal` int(11) DEFAULT '0',
  `Optical components Min` int(11) DEFAULT '0',
  `Optical components Max` int(11) DEFAULT '0',
  `Optical components Buy` int(11) DEFAULT '0',
  `Optical components Sell` int(11) DEFAULT '0',
  `Radioactive cells Amount` int(11) DEFAULT '0',
  `Radioactive cells Bal` int(11) DEFAULT '0',
  `Radioactive cells Min` int(11) DEFAULT '0',
  `Radioactive cells Max` int(11) DEFAULT '0',
  `Radioactive cells Buy` int(11) DEFAULT '0',
  `Radioactive cells Sell` int(11) DEFAULT '0',
  `Droid modules Amount` int(11) DEFAULT '0',
  `Droid modules Bal` int(11) DEFAULT '0',
  `Droid modules Min` int(11) DEFAULT '0',
  `Droid modules Max` int(11) DEFAULT '0',
  `Droid modules Buy` int(11) DEFAULT '0',
  `Droid modules Sell` int(11) DEFAULT '0',
  `Bio-waste Amount` int(11) DEFAULT '0',
  `Bio-waste Bal` int(11) DEFAULT '0',
  `Bio-waste Min` int(11) DEFAULT '0',
  `Bio-waste Max` int(11) DEFAULT '0',
  `Bio-waste Buy` int(11) DEFAULT '0',
  `Bio-waste Sell` int(11) DEFAULT '0',
  `Leech baby Amount` int(11) DEFAULT '0',
  `Leech baby Bal` int(11) DEFAULT '0',
  `Leech baby Min` int(11) DEFAULT '0',
  `Leech baby Max` int(11) DEFAULT '0',
  `Leech baby Buy` int(11) DEFAULT '0',
  `Leech baby Sell` int(11) DEFAULT '0',
  `Nutrient clods Amount` int(11) DEFAULT '0',
  `Nutrient clods Bal` int(11) DEFAULT '0',
  `Nutrient clods Min` int(11) DEFAULT '0',
  `Nutrient clods Max` int(11) DEFAULT '0',
  `Nutrient clods Buy` int(11) DEFAULT '0',
  `Nutrient clods Sell` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Amount` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Bal` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Min` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Max` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Buy` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Sell` int(11) DEFAULT '0',
  `X-993 Repair-Drone Amount` int(11) DEFAULT '0',
  `X-993 Repair-Drone Bal` int(11) DEFAULT '0',
  `X-993 Repair-Drone Min` int(11) DEFAULT '0',
  `X-993 Repair-Drone Max` int(11) DEFAULT '0',
  `X-993 Repair-Drone Buy` int(11) DEFAULT '0',
  `X-993 Repair-Drone Sell` int(11) DEFAULT '0',
  `Neural Stimulator Amount` int(11) DEFAULT '0',
  `Neural Stimulator Bal` int(11) DEFAULT '0',
  `Neural Stimulator Min` int(11) DEFAULT '0',
  `Neural Stimulator Max` int(11) DEFAULT '0',
  `Neural Stimulator Buy` int(11) DEFAULT '0',
  `Neural Stimulator Sell` int(11) DEFAULT '0',
  `Battleweapon Parts Amount` int(11) DEFAULT '0',
  `Battleweapon Parts Bal` int(11) DEFAULT '0',
  `Battleweapon Parts Min` int(11) DEFAULT '0',
  `Battleweapon Parts Max` int(11) DEFAULT '0',
  `Battleweapon Parts Buy` int(11) DEFAULT '0',
  `Battleweapon Parts Sell` int(11) DEFAULT '0',
  `Slaves Amount` int(11) DEFAULT '0',
  `Slaves Bal` int(11) DEFAULT '0',
  `Slaves Min` int(11) NOT NULL DEFAULT '0',
  `Slaves Max` int(11) DEFAULT '0',
  `Slaves Buy` int(11) DEFAULT '0',
  `Slaves Sell` int(11) DEFAULT '0',
  `Drugs Amount` int(11) DEFAULT '0',
  `Drugs Bal` int(11) DEFAULT '0',
  `Drugs Min` int(11) DEFAULT '0',
  `Drugs Max` int(11) DEFAULT '0',
  `Drugs Buy` int(11) DEFAULT '0',
  `Drugs Sell` int(11) DEFAULT '0',
  `Human intestines Amount` int(11) DEFAULT '0',
  `Human intestines Bal` int(11) DEFAULT '0',
  `Human intestines Min` int(11) DEFAULT '0',
  `Human intestines Max` int(11) DEFAULT '0',
  `Human intestines Buy` int(11) DEFAULT '0',
  `Human intestines Sell` int(11) DEFAULT '0',
  `Skaari limbs Amount` int(11) DEFAULT '0',
  `Skaari limbs Bal` int(11) DEFAULT '0',
  `Skaari limbs Min` int(11) DEFAULT '0',
  `Skaari limbs Max` int(11) DEFAULT '0',
  `Skaari limbs Buy` int(11) DEFAULT '0',
  `Skaari limbs Sell` int(11) DEFAULT '0',
  `Keldon brains Amount` int(11) DEFAULT '0',
  `Keldon brains Bal` int(11) DEFAULT '0',
  `Keldon brains Min` int(11) DEFAULT '0',
  `Keldon brains Max` int(11) DEFAULT '0',
  `Keldon brains Buy` int(11) DEFAULT '0',
  `Keldon brains Sell` int(11) DEFAULT '0',
  `Rashkir bones Amount` int(11) DEFAULT '0',
  `Rashkir bones Bal` int(11) DEFAULT '0',
  `Rashkir bones Min` int(11) DEFAULT '0',
  `Rashkir bones Max` int(11) DEFAULT '0',
  `Rashkir bones Buy` int(11) DEFAULT '0',
  `Rashkir bones Sell` int(11) DEFAULT '0',
  `Exotic Crystal Amount` int(11) DEFAULT '0',
  `Exotic Crystal Bal` int(11) DEFAULT '0',
  `Exotic Crystal Min` int(11) DEFAULT '0',
  `Exotic Crystal Max` int(11) DEFAULT '0',
  `Exotic Crystal Buy` int(11) DEFAULT '0',
  `Exotic Crystal Sell` int(11) DEFAULT '0',
  `Military Explosives Amount` int(11) DEFAULT '0',
  `Military Explosives Bal` int(11) DEFAULT '0',
  `Military Explosives Min` int(11) DEFAULT '0',
  `Military Explosives Max` int(11) DEFAULT '0',
  `Military Explosives Buy` int(11) DEFAULT '0',
  `Military Explosives Sell` int(11) DEFAULT '0',
  `Blue Sapphire jewels Amount` int(11) DEFAULT '0',
  `Blue Sapphire jewels Bal` int(11) DEFAULT '0',
  `Blue Sapphire jewels Min` int(11) DEFAULT '0',
  `Blue Sapphire jewels Max` int(11) DEFAULT '0',
  `Blue Sapphire jewels Buy` int(11) DEFAULT '0',
  `Blue Sapphire jewels Sell` int(11) DEFAULT '0',
  `Ruby jewels Amount` int(11) DEFAULT '0',
  `Ruby jewels Bal` int(11) DEFAULT '0',
  `Ruby jewels Min` int(11) DEFAULT '0',
  `Ruby jewels Max` int(11) DEFAULT '0',
  `Ruby jewels Buy` int(11) DEFAULT '0',
  `Ruby jewels Sell` int(11) DEFAULT '0',
  `Golden Beryl jewels Amount` int(11) DEFAULT '0',
  `Golden Beryl jewels Bal` int(11) DEFAULT '0',
  `Golden Beryl jewels Min` int(11) DEFAULT '0',
  `Golden Beryl jewels Max` int(11) DEFAULT '0',
  `Golden Beryl jewels Buy` int(11) DEFAULT '0',
  `Golden Beryl jewels Sell` int(11) DEFAULT '0',
  `date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_Test_Missions`
--

CREATE TABLE `Artemis_Test_Missions` (
  `id` int(11) NOT NULL,
  `source_id` int(4) DEFAULT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `loc` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(4) DEFAULT NULL,
  `y` tinyint(4) DEFAULT NULL,
  `comp` tinyint(4) DEFAULT NULL,
  `rank` tinyint(4) DEFAULT NULL,
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type_img` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `amount` smallint(6) UNSIGNED DEFAULT NULL,
  `hack` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `t_loc` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `t_cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `t_sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `t_x` tinyint(4) DEFAULT NULL,
  `t_y` tinyint(4) DEFAULT NULL,
  `time` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  `war` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_Test_Npcs`
--

CREATE TABLE `Artemis_Test_Npcs` (
  `kid` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `nid` int(11) DEFAULT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `cloaked` tinyint(4) DEFAULT NULL,
  `x` tinyint(11) DEFAULT NULL,
  `y` tinyint(11) DEFAULT NULL,
  `name` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `image` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `hull` int(11) DEFAULT '0',
  `armor` int(11) DEFAULT '0',
  `shield` int(11) DEFAULT '0',
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_Test_Stock`
--

CREATE TABLE `Artemis_Test_Stock` (
  `id` int(11) NOT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `x` tinyint(4) NOT NULL DEFAULT '0',
  `y` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `owner` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `alliance` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT '0',
  `Food` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Energy` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Water` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Animal embryos` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Ore` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Metal` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Electronics` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Robots` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Heavy plastics` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Hand weapons` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Medicines` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Nebula gas` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Chemical supplies` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Gem stones` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Liquor` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Hydrogen fuel` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Exotic matter` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Optical components` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Radioactive cells` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Droid modules` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Bio-waste` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Leech baby` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Nutrient clods` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Cybernetic X-993 Parts` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `X-993 Repair-Drone` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Neural Stimulator` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Battleweapon Parts` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Slaves` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Drugs` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Human intestines` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Skaari limbs` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Keldon brains` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Rashkir bones` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Exotic Crystal` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Military Explosives` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Blue Sapphire jewels` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Ruby jewels` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Golden Beryl jewels` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Artemis_Users`
--

CREATE TABLE `Artemis_Users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `id` int(11) NOT NULL DEFAULT '0',
  `username` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `password` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `security` tinyint(4) NOT NULL DEFAULT '0',
  `login` datetime DEFAULT NULL,
  `logout` datetime DEFAULT NULL,
  `loaded` datetime DEFAULT NULL,
  `version` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `browser` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `syndicate` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `comp` tinyint(4) DEFAULT NULL,
  `rank` tinyint(4) DEFAULT NULL,
  `imagepack` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `ip` varchar(15) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `background`
--

CREATE TABLE `background` (
  `id` int(11) NOT NULL,
  `sector` varchar(20) NOT NULL,
  `x` tinyint(3) UNSIGNED NOT NULL,
  `y` tinyint(3) UNSIGNED NOT NULL,
  `image` varchar(30) NOT NULL,
  `offset` smallint(6) NOT NULL,
  `type` varchar(25) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `id` int(11) NOT NULL,
  `uni` set('Orion','Artemis','Pegasus') COLLATE latin1_general_ci NOT NULL,
  `trade` longtext COLLATE latin1_general_ci NOT NULL,
  `date` datetime DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `stock_updated` datetime DEFAULT NULL,
  `eq_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `connection_log`
--

CREATE TABLE `connection_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `universe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `querycount` int(10) UNSIGNED DEFAULT '0',
  `duration` float(6,2) UNSIGNED DEFAULT '0.00',
  `date` datetime NOT NULL,
  `payload` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Data Differences`
--

CREATE TABLE `Data Differences` (
  `id` int(11) NOT NULL,
  `sector` varchar(50) NOT NULL,
  `x` int(3) NOT NULL,
  `y` int(3) NOT NULL,
  `am` varchar(500) NOT NULL,
  `pm` varchar(500) NOT NULL,
  `om` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `foreground`
--

CREATE TABLE `foreground` (
  `id` int(11) NOT NULL,
  `code` varchar(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '0',
  `cluster` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `sector` varchar(25) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL,
  `fg` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `npc` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `date` datetime NOT NULL,
  `added` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Buildings`
--

CREATE TABLE `Orion_Buildings` (
  `id` int(11) NOT NULL,
  `cluster` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(25) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(4) NOT NULL DEFAULT '0',
  `y` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `image` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `owner` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `alliance` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `stock` tinyint(4) NOT NULL DEFAULT '0',
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `population` int(11) NOT NULL DEFAULT '0',
  `crime` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `condition` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `capacity` int(11) NOT NULL DEFAULT '0',
  `freespace` int(11) NOT NULL DEFAULT '0',
  `credit` bigint(11) NOT NULL DEFAULT '0',
  `security` int(11) NOT NULL DEFAULT '0',
  `starbase` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `stock_updated` datetime DEFAULT NULL,
  `eq_updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Crew`
--

CREATE TABLE `Orion_Crew` (
  `name` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `loc` int(11) DEFAULT NULL,
  `type` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `title` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `job1` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `job2` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `level` smallint(6) DEFAULT NULL,
  `image` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `fee` int(11) DEFAULT NULL,
  `pay` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Equipment`
--

CREATE TABLE `Orion_Equipment` (
  `name` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `loc` int(11) NOT NULL DEFAULT '0',
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `image` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `type` set('weapon','drive','armor','shield','special','ship') COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Maps`
--

CREATE TABLE `Orion_Maps` (
  `id` int(11) NOT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(4) NOT NULL DEFAULT '0',
  `y` tinyint(4) NOT NULL DEFAULT '0',
  `bg` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `fg` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `fg_spotted` datetime DEFAULT NULL,
  `fg_updated` datetime DEFAULT NULL,
  `npc` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `npc_cloaked` tinyint(4) DEFAULT NULL,
  `npc_spotted` datetime DEFAULT NULL,
  `npc_updated` datetime DEFAULT NULL,
  `wormhole` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `starbase` tinyint(4) NOT NULL DEFAULT '0',
  `security` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Missions`
--

CREATE TABLE `Orion_Missions` (
  `id` int(11) NOT NULL,
  `loc` int(11) NOT NULL,
  `comp` tinyint(4) NOT NULL,
  `rank` tinyint(4) DEFAULT NULL,
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type_img` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `target` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(4) DEFAULT NULL,
  `y` tinyint(4) DEFAULT NULL,
  `time` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_New_Stock`
--

CREATE TABLE `Orion_New_Stock` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `bal` int(11) NOT NULL DEFAULT '0',
  `min` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `buy` int(11) NOT NULL DEFAULT '0',
  `sell` int(11) NOT NULL DEFAULT '0',
  `stock` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Npcs`
--

CREATE TABLE `Orion_Npcs` (
  `id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `image` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `hull` int(11) NOT NULL DEFAULT '0',
  `armor` int(11) NOT NULL DEFAULT '0',
  `shield` int(11) NOT NULL DEFAULT '0',
  `date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Personal_Resources`
--

CREATE TABLE `Orion_Personal_Resources` (
  `id` int(11) NOT NULL,
  `loc` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Squadrons`
--

CREATE TABLE `Orion_Squadrons` (
  `id` int(11) NOT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(4) NOT NULL DEFAULT '0',
  `y` tinyint(4) NOT NULL DEFAULT '0',
  `image` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `type` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `weapons` int(11) NOT NULL,
  `credit` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Stock`
--

CREATE TABLE `Orion_Stock` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `bal` int(11) NOT NULL DEFAULT '0',
  `min` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `buy` int(11) NOT NULL DEFAULT '0',
  `sell` int(11) NOT NULL DEFAULT '0',
  `stock` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Test_Missions`
--

CREATE TABLE `Orion_Test_Missions` (
  `id` int(11) NOT NULL,
  `source_id` int(4) DEFAULT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `loc` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(4) DEFAULT NULL,
  `y` tinyint(4) DEFAULT NULL,
  `comp` tinyint(4) DEFAULT NULL,
  `rank` tinyint(4) DEFAULT NULL,
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type_img` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `amount` smallint(5) UNSIGNED DEFAULT NULL,
  `hack` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `t_loc` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `t_cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `t_sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `t_x` tinyint(4) DEFAULT NULL,
  `t_y` tinyint(4) DEFAULT NULL,
  `time` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  `war` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Test_Npcs`
--

CREATE TABLE `Orion_Test_Npcs` (
  `kid` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `nid` int(11) DEFAULT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `cloaked` tinyint(4) DEFAULT NULL,
  `x` tinyint(11) DEFAULT NULL,
  `y` tinyint(11) DEFAULT NULL,
  `name` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `image` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `hull` int(11) DEFAULT '0',
  `armor` int(11) DEFAULT '0',
  `shield` int(11) DEFAULT '0',
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Test_Stock`
--

CREATE TABLE `Orion_Test_Stock` (
  `id` int(11) NOT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `x` tinyint(4) NOT NULL DEFAULT '0',
  `y` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `owner` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `alliance` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT '0',
  `Food` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Food_level` tinyint(4) NOT NULL DEFAULT '0',
  `Energy` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Energy_level` tinyint(4) NOT NULL DEFAULT '0',
  `Water` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Water_level` tinyint(4) NOT NULL DEFAULT '0',
  `Animal embryos` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Animal embryos_level` tinyint(4) NOT NULL DEFAULT '0',
  `Ore` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Ore_level` tinyint(4) NOT NULL DEFAULT '0',
  `Metal` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Metal_level` tinyint(4) NOT NULL DEFAULT '0',
  `Electronics` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Electronics_level` tinyint(4) NOT NULL DEFAULT '0',
  `Robots` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Robots_level` tinyint(4) NOT NULL DEFAULT '0',
  `Heavy plastics` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Heavy plastics_level` tinyint(4) NOT NULL DEFAULT '0',
  `Hand weapons` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Hand weapons_level` tinyint(4) NOT NULL DEFAULT '0',
  `Medicines` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Medicines_level` tinyint(4) NOT NULL DEFAULT '0',
  `Nebula gas` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Nebula gas_level` tinyint(4) NOT NULL DEFAULT '0',
  `Chemical supplies` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Chemical supplies_level` tinyint(4) NOT NULL DEFAULT '0',
  `Gem stones` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Gem stones_level` tinyint(4) NOT NULL DEFAULT '0',
  `Liquor` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Liquor_level` tinyint(4) NOT NULL DEFAULT '0',
  `Hydrogen fuel` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Hydrogen fuel_level` tinyint(4) NOT NULL DEFAULT '0',
  `Exotic matter` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Exotic matter_level` tinyint(4) NOT NULL DEFAULT '0',
  `Optical components` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Optical components_level` tinyint(4) NOT NULL DEFAULT '0',
  `Radioactive cells` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Radioactive cells_level` tinyint(4) NOT NULL DEFAULT '0',
  `Droid modules` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Droid modules_level` tinyint(4) NOT NULL DEFAULT '0',
  `Bio-waste` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Bio-waste_level` tinyint(4) NOT NULL DEFAULT '0',
  `Leech baby` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Leech baby_level` tinyint(4) NOT NULL DEFAULT '0',
  `Nutrient clods` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Nutrient clods_level` tinyint(4) NOT NULL DEFAULT '0',
  `Cybernetic X-993 Parts` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Cybernetic X-993 Parts_level` tinyint(4) NOT NULL DEFAULT '0',
  `X-993 Repair-Drone` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `X-993 Repair-Drone_level` tinyint(4) NOT NULL DEFAULT '0',
  `Neural Stimulator` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Neural Stimulator_level` tinyint(4) NOT NULL DEFAULT '0',
  `Battleweapon Parts` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Battleweapon Parts_level` tinyint(4) NOT NULL DEFAULT '0',
  `Slaves` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Slaves_level` tinyint(4) NOT NULL DEFAULT '0',
  `Drugs` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Drugs_level` tinyint(4) NOT NULL DEFAULT '0',
  `Human intestines` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Human intestines_level` tinyint(4) NOT NULL DEFAULT '0',
  `Skaari limbs` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Skaari limbs_level` tinyint(4) NOT NULL DEFAULT '0',
  `Keldon brains` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Keldon brains_level` tinyint(4) NOT NULL DEFAULT '0',
  `Rashkir bones` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Rashkir bones_level` tinyint(4) NOT NULL DEFAULT '0',
  `Exotic Crystal` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Exotic Crystal_level` tinyint(4) NOT NULL DEFAULT '0',
  `Military Explosives` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Military Explosives_level` tinyint(4) NOT NULL DEFAULT '0',
  `Blue Sapphire jewels` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Blue Sapphire jewels_level` tinyint(4) NOT NULL DEFAULT '0',
  `Ruby jewels` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Ruby jewels_level` tinyint(4) NOT NULL DEFAULT '0',
  `Golden Beryl jewels` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Golden Beryl jewels_level` tinyint(4) NOT NULL DEFAULT '0',
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Orion_Users`
--

CREATE TABLE `Orion_Users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `id` int(11) NOT NULL DEFAULT '0',
  `username` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `password` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `security` tinyint(4) NOT NULL DEFAULT '0',
  `keys` varchar(200) COLLATE latin1_general_ci DEFAULT '*|0',
  `login` datetime DEFAULT NULL,
  `logout` datetime DEFAULT NULL,
  `loaded` datetime DEFAULT NULL,
  `version` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `browser` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `syndicate` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `comp` tinyint(4) DEFAULT NULL,
  `rank` tinyint(4) DEFAULT NULL,
  `imagepack` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `ip` varchar(15) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pardus_Buildings_Data`
--

CREATE TABLE `Pardus_Buildings_Data` (
  `name` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `image` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `image2` varchar(50) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pardus_Clusters`
--

CREATE TABLE `Pardus_Clusters` (
  `c_id` int(11) NOT NULL,
  `name` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `code` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `r_id` int(11) DEFAULT NULL COMMENT 'Resource ID',
  `f_id` int(11) NOT NULL,
  `row` tinyint(4) NOT NULL DEFAULT '0',
  `col` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pardus_Hash`
--

CREATE TABLE `Pardus_Hash` (
  `function` varchar(20) NOT NULL,
  `value` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Pardus_Maps`
--

CREATE TABLE `Pardus_Maps` (
  `id` int(11) NOT NULL,
  `bg` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `wormhole` varchar(50) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pardus_Npcs`
--

CREATE TABLE `Pardus_Npcs` (
  `name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `image` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `image2` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `xp` int(11) NOT NULL DEFAULT '0',
  `hull` int(11) NOT NULL DEFAULT '0',
  `armor` int(11) NOT NULL DEFAULT '0',
  `str` tinyint(4) NOT NULL DEFAULT '0',
  `type` set('none','conv','em','org','pardus') COLLATE latin1_general_ci NOT NULL DEFAULT 'none',
  `shield` int(11) NOT NULL DEFAULT '0',
  `premium` tinyint(4) NOT NULL DEFAULT '0',
  `tac` tinyint(4) NOT NULL DEFAULT '0',
  `ha` tinyint(4) NOT NULL DEFAULT '0',
  `man` tinyint(4) NOT NULL DEFAULT '0',
  `wep` tinyint(4) NOT NULL DEFAULT '0',
  `eng` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pardus_Res_Data`
--

CREATE TABLE `Pardus_Res_Data` (
  `r_id` int(11) NOT NULL,
  `name` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `worker_bonus` decimal(4,4) NOT NULL,
  `s_econ` tinyint(1) NOT NULL,
  `image` varchar(50) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pardus_Sectors`
--

CREATE TABLE `Pardus_Sectors` (
  `s_id` int(11) NOT NULL,
  `sid` int(11) NOT NULL DEFAULT '0',
  `cluster` varchar(5) COLLATE latin1_general_ci DEFAULT NULL,
  `c_name` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `name` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `c_id` int(11) NOT NULL,
  `rows` int(2) NOT NULL,
  `cols` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `Pardus_Ship_Data`
--

CREATE TABLE `Pardus_Ship_Data` (
  `name` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `image` varchar(50) COLLATE latin1_general_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pardus_Static_Locations`
--

CREATE TABLE `Pardus_Static_Locations` (
  `id` int(11) NOT NULL,
  `sector` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Pardus_Upkeep_Data`
--

CREATE TABLE `Pardus_Upkeep_Data` (
  `name` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `res` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `upkeep` tinyint(1) NOT NULL,
  `amount` int(11) NOT NULL,
  `hide` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_Buildings`
--

CREATE TABLE `Pegasus_Buildings` (
  `id` int(11) NOT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(11) NOT NULL DEFAULT '0',
  `y` tinyint(11) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `image` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `owner` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `alliance` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `stock` tinyint(4) NOT NULL DEFAULT '0',
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `population` int(11) DEFAULT NULL,
  `crime` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `condition` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `freespace` int(11) DEFAULT NULL,
  `credit` bigint(11) DEFAULT NULL,
  `security` int(11) NOT NULL DEFAULT '0',
  `starbase` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `stock_updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_Maps`
--

CREATE TABLE `Pegasus_Maps` (
  `id` int(11) NOT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(4) NOT NULL DEFAULT '0',
  `y` tinyint(4) NOT NULL DEFAULT '0',
  `bg` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `fg` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `fg_spotted` datetime DEFAULT NULL,
  `fg_updated` datetime DEFAULT NULL,
  `npc` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `npc_cloaked` tinyint(4) DEFAULT NULL,
  `npc_spotted` datetime DEFAULT NULL,
  `npc_updated` datetime DEFAULT NULL,
  `wormhole` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `starbase` tinyint(4) NOT NULL DEFAULT '0',
  `security` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_Missions`
--

CREATE TABLE `Pegasus_Missions` (
  `id` int(11) NOT NULL,
  `loc` int(11) NOT NULL,
  `comp` tinyint(4) NOT NULL,
  `rank` tinyint(4) DEFAULT NULL,
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type_img` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `amount` tinyint(4) DEFAULT NULL,
  `target` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(4) DEFAULT NULL,
  `y` tinyint(4) DEFAULT NULL,
  `time` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_New_Stock`
--

CREATE TABLE `Pegasus_New_Stock` (
  `id` int(11) NOT NULL,
  `name` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `bal` int(11) NOT NULL DEFAULT '0',
  `min` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `buy` int(11) NOT NULL DEFAULT '0',
  `sell` int(11) NOT NULL DEFAULT '0',
  `stock` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_Npcs`
--

CREATE TABLE `Pegasus_Npcs` (
  `id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `image` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `hull` int(11) DEFAULT NULL,
  `armor` int(11) DEFAULT NULL,
  `shield` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_Personal_Resources`
--

CREATE TABLE `Pegasus_Personal_Resources` (
  `id` int(11) NOT NULL,
  `loc` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_Squadrons`
--

CREATE TABLE `Pegasus_Squadrons` (
  `id` int(11) NOT NULL,
  `image` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `type` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `weapons` int(11) NOT NULL,
  `credit` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_Stock`
--

CREATE TABLE `Pegasus_Stock` (
  `id` int(11) NOT NULL,
  `Food Amount` int(11) DEFAULT '0',
  `Food Bal` int(11) DEFAULT '0',
  `Food Min` int(11) DEFAULT '0',
  `Food Max` int(11) DEFAULT '0',
  `Food Buy` int(11) DEFAULT '0',
  `Food Sell` int(11) DEFAULT '0',
  `Energy Amount` int(11) DEFAULT '0',
  `Energy Bal` int(11) DEFAULT '0',
  `Energy Min` int(11) DEFAULT '0',
  `Energy Max` int(11) DEFAULT '0',
  `Energy Buy` int(11) DEFAULT '0',
  `Energy Sell` int(11) DEFAULT '0',
  `Water Amount` int(11) DEFAULT '0',
  `Water Bal` int(11) DEFAULT '0',
  `Water Min` int(11) DEFAULT '0',
  `Water Max` int(11) DEFAULT '0',
  `Water Buy` int(11) DEFAULT '0',
  `Water Sell` int(11) DEFAULT '0',
  `Animal embryos Amount` int(11) DEFAULT '0',
  `Animal embryos Bal` int(11) DEFAULT '0',
  `Animal embryos Min` int(11) DEFAULT '0',
  `Animal embryos Max` int(11) DEFAULT '0',
  `Animal embryos Buy` int(11) DEFAULT '0',
  `Animal embryos Sell` int(11) DEFAULT '0',
  `Ore Amount` int(11) DEFAULT '0',
  `Ore Bal` int(11) DEFAULT '0',
  `Ore Min` int(11) DEFAULT '0',
  `Ore Max` int(11) DEFAULT '0',
  `Ore Buy` int(11) DEFAULT '0',
  `Ore Sell` int(11) DEFAULT '0',
  `Metal Amount` int(11) DEFAULT '0',
  `Metal Bal` int(11) DEFAULT '0',
  `Metal Min` int(11) DEFAULT '0',
  `Metal Max` int(11) DEFAULT '0',
  `Metal Buy` int(11) DEFAULT '0',
  `Metal Sell` int(11) DEFAULT '0',
  `Electronics Amount` int(11) DEFAULT '0',
  `Electronics Bal` int(11) DEFAULT '0',
  `Electronics Min` int(11) DEFAULT '0',
  `Electronics Max` int(11) DEFAULT '0',
  `Electronics Buy` int(11) DEFAULT '0',
  `Electronics Sell` int(11) DEFAULT '0',
  `Robots Amount` int(11) DEFAULT '0',
  `Robots Bal` int(11) DEFAULT '0',
  `Robots Min` int(11) DEFAULT '0',
  `Robots Max` int(11) DEFAULT '0',
  `Robots Buy` int(11) DEFAULT '0',
  `Robots Sell` int(11) DEFAULT '0',
  `Heavy plastics Amount` int(11) DEFAULT '0',
  `Heavy plastics Bal` int(11) DEFAULT '0',
  `Heavy plastics Min` int(11) DEFAULT '0',
  `Heavy plastics Max` int(11) DEFAULT '0',
  `Heavy plastics Buy` int(11) DEFAULT '0',
  `Heavy plastics Sell` int(11) DEFAULT '0',
  `Hand weapons Amount` int(11) DEFAULT '0',
  `Hand weapons Bal` int(11) DEFAULT '0',
  `Hand weapons Min` int(11) DEFAULT '0',
  `Hand weapons Max` int(11) DEFAULT '0',
  `Hand weapons Buy` int(11) DEFAULT '0',
  `Hand weapons Sell` int(11) DEFAULT '0',
  `Medicines Amount` int(11) DEFAULT '0',
  `Medicines Bal` int(11) DEFAULT '0',
  `Medicines Min` int(11) DEFAULT '0',
  `Medicines Max` int(11) DEFAULT '0',
  `Medicines Buy` int(11) DEFAULT '0',
  `Medicines Sell` int(11) DEFAULT '0',
  `Nebula gas Amount` int(11) DEFAULT '0',
  `Nebula Gas Bal` int(11) DEFAULT '0',
  `Nebula gas Min` int(11) DEFAULT '0',
  `Nebula gas Max` int(11) DEFAULT '0',
  `Nebula gas Buy` int(11) DEFAULT '0',
  `Nebula gas Sell` int(11) DEFAULT '0',
  `Chemical supplies Amount` int(11) DEFAULT '0',
  `Chemical supplies Bal` int(11) DEFAULT '0',
  `Chemical supplies Min` int(11) DEFAULT '0',
  `Chemical supplies Max` int(11) DEFAULT '0',
  `Chemical supplies Buy` int(11) DEFAULT '0',
  `Chemical supplies Sell` int(11) DEFAULT '0',
  `Gem stones Amount` int(11) DEFAULT '0',
  `Gem stones Bal` int(11) DEFAULT '0',
  `Gem stones Min` int(11) DEFAULT '0',
  `Gem stones Max` int(11) DEFAULT '0',
  `Gem stones Buy` int(11) DEFAULT '0',
  `Gem stones Sell` int(11) DEFAULT '0',
  `Liquor Amount` int(11) DEFAULT '0',
  `Liquor Bal` int(11) DEFAULT '0',
  `Liquor Min` int(11) DEFAULT '0',
  `Liquor Max` int(11) DEFAULT '0',
  `Liquor Buy` int(11) DEFAULT '0',
  `Liquor Sell` int(11) DEFAULT '0',
  `Hydrogen fuel Amount` int(11) DEFAULT '0',
  `Hydrogen fuel Bal` int(11) DEFAULT '0',
  `Hydrogen fuel Min` int(11) DEFAULT '0',
  `Hydrogen fuel Max` int(11) DEFAULT '0',
  `Hydrogen fuel Buy` int(11) DEFAULT '0',
  `Hydrogen fuel Sell` int(11) DEFAULT '0',
  `Exotic matter Amount` int(11) DEFAULT '0',
  `Exotic matter Bal` int(11) DEFAULT '0',
  `Exotic matter Min` int(11) DEFAULT '0',
  `Exotic matter Max` int(11) DEFAULT '0',
  `Exotic matter Buy` int(11) DEFAULT '0',
  `Exotic matter Sell` int(11) DEFAULT '0',
  `Optical components Amount` int(11) DEFAULT '0',
  `Optical components Bal` int(11) DEFAULT '0',
  `Optical components Min` int(11) DEFAULT '0',
  `Optical components Max` int(11) DEFAULT '0',
  `Optical components Buy` int(11) DEFAULT '0',
  `Optical components Sell` int(11) DEFAULT '0',
  `Radioactive cells Amount` int(11) DEFAULT '0',
  `Radioactive cells Bal` int(11) DEFAULT '0',
  `Radioactive cells Min` int(11) DEFAULT '0',
  `Radioactive cells Max` int(11) DEFAULT '0',
  `Radioactive cells Buy` int(11) DEFAULT '0',
  `Radioactive cells Sell` int(11) DEFAULT '0',
  `Droid modules Amount` int(11) DEFAULT '0',
  `Droid modules Bal` int(11) DEFAULT '0',
  `Droid modules Min` int(11) DEFAULT '0',
  `Droid modules Max` int(11) DEFAULT '0',
  `Droid modules Buy` int(11) DEFAULT '0',
  `Droid modules Sell` int(11) DEFAULT '0',
  `Bio-waste Amount` int(11) DEFAULT '0',
  `Bio-waste Bal` int(11) DEFAULT '0',
  `Bio-waste Min` int(11) DEFAULT '0',
  `Bio-waste Max` int(11) DEFAULT '0',
  `Bio-waste Buy` int(11) DEFAULT '0',
  `Bio-waste Sell` int(11) DEFAULT '0',
  `Leech baby Amount` int(11) DEFAULT '0',
  `Leech baby Bal` int(11) DEFAULT '0',
  `Leech baby Min` int(11) DEFAULT '0',
  `Leech baby Max` int(11) DEFAULT '0',
  `Leech baby Buy` int(11) DEFAULT '0',
  `Leech baby Sell` int(11) DEFAULT '0',
  `Nutrient clods Amount` int(11) DEFAULT '0',
  `Nutrient clods Bal` int(11) DEFAULT '0',
  `Nutrient clods Min` int(11) DEFAULT '0',
  `Nutrient clods Max` int(11) DEFAULT '0',
  `Nutrient clods Buy` int(11) DEFAULT '0',
  `Nutrient clods Sell` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Amount` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Bal` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Min` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Max` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Buy` int(11) DEFAULT '0',
  `Cybernetic X-993 Parts Sell` int(11) DEFAULT '0',
  `X-993 Repair-Drone Amount` int(11) DEFAULT '0',
  `X-993 Repair-Drone Bal` int(11) DEFAULT '0',
  `X-993 Repair-Drone Min` int(11) DEFAULT '0',
  `X-993 Repair-Drone Max` int(11) DEFAULT '0',
  `X-993 Repair-Drone Buy` int(11) DEFAULT '0',
  `X-993 Repair-Drone Sell` int(11) DEFAULT '0',
  `Neural Stimulator Amount` int(11) DEFAULT '0',
  `Neural Stimulator Bal` int(11) DEFAULT '0',
  `Neural Stimulator Min` int(11) DEFAULT '0',
  `Neural Stimulator Max` int(11) DEFAULT '0',
  `Neural Stimulator Buy` int(11) DEFAULT '0',
  `Neural Stimulator Sell` int(11) DEFAULT '0',
  `Battleweapon Parts Amount` int(11) DEFAULT '0',
  `Battleweapon Parts Bal` int(11) DEFAULT '0',
  `Battleweapon Parts Min` int(11) DEFAULT '0',
  `Battleweapon Parts Max` int(11) DEFAULT '0',
  `Battleweapon Parts Buy` int(11) DEFAULT '0',
  `Battleweapon Parts Sell` int(11) DEFAULT '0',
  `Slaves Amount` int(11) DEFAULT '0',
  `Slaves Bal` int(11) DEFAULT '0',
  `Slaves Min` int(11) NOT NULL DEFAULT '0',
  `Slaves Max` int(11) DEFAULT '0',
  `Slaves Buy` int(11) DEFAULT '0',
  `Slaves Sell` int(11) DEFAULT '0',
  `Drugs Amount` int(11) DEFAULT '0',
  `Drugs Bal` int(11) DEFAULT '0',
  `Drugs Min` int(11) DEFAULT '0',
  `Drugs Max` int(11) DEFAULT '0',
  `Drugs Buy` int(11) DEFAULT '0',
  `Drugs Sell` int(11) DEFAULT '0',
  `Human intestines Amount` int(11) DEFAULT '0',
  `Human intestines Bal` int(11) DEFAULT '0',
  `Human intestines Min` int(11) DEFAULT '0',
  `Human intestines Max` int(11) DEFAULT '0',
  `Human intestines Buy` int(11) DEFAULT '0',
  `Human intestines Sell` int(11) DEFAULT '0',
  `Skaari limbs Amount` int(11) DEFAULT '0',
  `Skaari limbs Bal` int(11) DEFAULT '0',
  `Skaari limbs Min` int(11) DEFAULT '0',
  `Skaari limbs Max` int(11) DEFAULT '0',
  `Skaari limbs Buy` int(11) DEFAULT '0',
  `Skaari limbs Sell` int(11) DEFAULT '0',
  `Keldon brains Amount` int(11) DEFAULT '0',
  `Keldon brains Bal` int(11) DEFAULT '0',
  `Keldon brains Min` int(11) DEFAULT '0',
  `Keldon brains Max` int(11) DEFAULT '0',
  `Keldon brains Buy` int(11) DEFAULT '0',
  `Keldon brains Sell` int(11) DEFAULT '0',
  `Rashkir bones Amount` int(11) DEFAULT '0',
  `Rashkir bones Bal` int(11) DEFAULT '0',
  `Rashkir bones Min` int(11) DEFAULT '0',
  `Rashkir bones Max` int(11) DEFAULT '0',
  `Rashkir bones Buy` int(11) DEFAULT '0',
  `Rashkir bones Sell` int(11) DEFAULT '0',
  `Exotic Crystal Amount` int(11) DEFAULT '0',
  `Exotic Crystal Bal` int(11) DEFAULT '0',
  `Exotic Crystal Min` int(11) DEFAULT '0',
  `Exotic Crystal Max` int(11) DEFAULT '0',
  `Exotic Crystal Buy` int(11) DEFAULT '0',
  `Exotic Crystal Sell` int(11) DEFAULT '0',
  `Military Explosives Amount` int(11) DEFAULT '0',
  `Military Explosives Bal` int(11) DEFAULT '0',
  `Military Explosives Min` int(11) DEFAULT '0',
  `Military Explosives Max` int(11) DEFAULT '0',
  `Military Explosives Buy` int(11) DEFAULT '0',
  `Military Explosives Sell` int(11) DEFAULT '0',
  `Blue Sapphire jewels Amount` int(11) DEFAULT '0',
  `Blue Sapphire jewels Bal` int(11) DEFAULT '0',
  `Blue Sapphire jewels Min` int(11) DEFAULT '0',
  `Blue Sapphire jewels Max` int(11) DEFAULT '0',
  `Blue Sapphire jewels Buy` int(11) DEFAULT '0',
  `Blue Sapphire jewels Sell` int(11) DEFAULT '0',
  `Ruby jewels Amount` int(11) DEFAULT '0',
  `Ruby jewels Bal` int(11) DEFAULT '0',
  `Ruby jewels Min` int(11) DEFAULT '0',
  `Ruby jewels Max` int(11) DEFAULT '0',
  `Ruby jewels Buy` int(11) DEFAULT '0',
  `Ruby jewels Sell` int(11) DEFAULT '0',
  `Golden Beryl jewels Amount` int(11) DEFAULT '0',
  `Golden Beryl jewels Bal` int(11) DEFAULT '0',
  `Golden Beryl jewels Min` int(11) DEFAULT '0',
  `Golden Beryl jewels Max` int(11) DEFAULT '0',
  `Golden Beryl jewels Buy` int(11) DEFAULT '0',
  `Golden Beryl jewels Sell` int(11) DEFAULT '0',
  `date` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_Test_Missions`
--

CREATE TABLE `Pegasus_Test_Missions` (
  `id` int(11) NOT NULL,
  `source_id` int(4) DEFAULT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `loc` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `x` tinyint(4) DEFAULT NULL,
  `y` tinyint(4) DEFAULT NULL,
  `comp` tinyint(4) DEFAULT NULL,
  `rank` tinyint(4) DEFAULT NULL,
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `type_img` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `amount` mediumint(8) UNSIGNED DEFAULT NULL,
  `hack` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `t_loc` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `t_cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `t_sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `t_x` tinyint(4) DEFAULT NULL,
  `t_y` tinyint(4) DEFAULT NULL,
  `time` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  `war` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_Test_Npcs`
--

CREATE TABLE `Pegasus_Test_Npcs` (
  `kid` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `nid` int(11) DEFAULT NULL,
  `cluster` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci DEFAULT NULL,
  `cloaked` tinyint(4) DEFAULT NULL,
  `x` tinyint(11) DEFAULT NULL,
  `y` tinyint(11) DEFAULT NULL,
  `name` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
  `image` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `hull` int(11) DEFAULT '0',
  `armor` int(11) DEFAULT '0',
  `shield` int(11) DEFAULT '0',
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_Test_Stock`
--

CREATE TABLE `Pegasus_Test_Stock` (
  `id` int(11) NOT NULL,
  `sector` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `x` tinyint(4) NOT NULL DEFAULT '0',
  `y` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `owner` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `alliance` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT '0',
  `Food` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Energy` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Water` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Animal embryos` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Ore` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Metal` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Electronics` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Robots` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Heavy plastics` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Hand weapons` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Medicines` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Nebula gas` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Chemical supplies` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Gem stones` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Liquor` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Hydrogen fuel` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Exotic matter` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Optical components` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Radioactive cells` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Droid modules` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Bio-waste` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Leech baby` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Nutrient clods` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Cybernetic X-993 Parts` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `X-993 Repair-Drone` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Neural Stimulator` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Battleweapon Parts` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Slaves` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Drugs` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Human intestines` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Skaari limbs` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Keldon brains` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Rashkir bones` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Exotic Crystal` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Military Explosives` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Blue Sapphire jewels` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Ruby jewels` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `Golden Beryl jewels` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '0,0,0,0,0,0,0',
  `spotted` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Pegasus_Users`
--

CREATE TABLE `Pegasus_Users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `id` int(11) NOT NULL DEFAULT '0',
  `username` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `password` varchar(50) COLLATE latin1_general_ci NOT NULL DEFAULT '',
  `security` tinyint(4) NOT NULL DEFAULT '0',
  `login` datetime DEFAULT NULL,
  `logout` datetime DEFAULT NULL,
  `loaded` datetime DEFAULT NULL,
  `version` varchar(10) COLLATE latin1_general_ci DEFAULT NULL,
  `browser` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `faction` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `syndicate` varchar(100) COLLATE latin1_general_ci DEFAULT NULL,
  `comp` tinyint(4) DEFAULT NULL,
  `rank` tinyint(4) DEFAULT NULL,
  `imagepack` varchar(200) COLLATE latin1_general_ci DEFAULT NULL,
  `ip` varchar(15) COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `v2_Pardus_NPC_Stats`
--

CREATE TABLE `v2_Pardus_NPC_Stats` (
  `name` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `image` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `xp` int(11) NOT NULL DEFAULT '0',
  `hull` int(11) NOT NULL DEFAULT '0',
  `armor` int(11) NOT NULL DEFAULT '0',
  `str` tinyint(4) NOT NULL DEFAULT '0',
  `type` set('none','conv','em','org','pardus') COLLATE latin1_general_ci NOT NULL DEFAULT 'none',
  `shield` int(11) NOT NULL DEFAULT '0',
  `premium` tinyint(4) NOT NULL DEFAULT '0',
  `tac` tinyint(4) NOT NULL DEFAULT '0',
  `ha` tinyint(4) NOT NULL DEFAULT '0',
  `man` tinyint(4) NOT NULL DEFAULT '0',
  `wep` tinyint(4) NOT NULL DEFAULT '0',
  `eng` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `v2_Pardus_Sectors`
--

CREATE TABLE `v2_Pardus_Sectors` (
  `sid` int(11) NOT NULL DEFAULT '0',
  `code` varchar(5) CHARACTER SET utf8 DEFAULT NULL,
  `stat` varchar(5) CHARACTER SET utf8 NOT NULL,
  `cluster` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `name` varchar(35) CHARACTER SET utf8 NOT NULL,
  `rows` int(2) NOT NULL,
  `cols` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Artemis_Building_Info`
--

CREATE TABLE `v3_Artemis_Building_Info` (
  `bik` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `code` varchar(15) NOT NULL DEFAULT '000000000000000',
  `info` longtext,
  `trade` longtext,
  `special` longtext,
  `added` datetime DEFAULT NULL,
  `info_updated` datetime DEFAULT NULL,
  `stock_updated` datetime DEFAULT NULL,
  `eq_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Artemis_Building_Stock`
--

CREATE TABLE `v3_Artemis_Building_Stock` (
  `bik` int(11) NOT NULL,
  `name` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `min` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `hide` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Artemis_Map_Buildings`
--

CREATE TABLE `v3_Artemis_Map_Buildings` (
  `id` int(11) NOT NULL,
  `cluster` varchar(25) DEFAULT NULL,
  `sector` varchar(25) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Artemis_Map_Npcs`
--

CREATE TABLE `v3_Artemis_Map_Npcs` (
  `id` int(11) NOT NULL,
  `cluster` varchar(25) DEFAULT NULL,
  `sector` varchar(25) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `title` varchar(35) DEFAULT NULL,
  `cloaked` tinyint(4) DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Artemis_Npc_Info`
--

CREATE TABLE `v3_Artemis_Npc_Info` (
  `id` int(11) NOT NULL,
  `nik` int(11) NOT NULL,
  `code` varchar(15) NOT NULL DEFAULT '000000000000000',
  `info` longtext CHARACTER SET latin1 COLLATE latin1_general_ci,
  `added` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Artemis_Pilot_Codes`
--

CREATE TABLE `v3_Artemis_Pilot_Codes` (
  `id` int(11) UNSIGNED NOT NULL,
  `sector` varchar(25) NOT NULL,
  `code` varchar(35) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Artemis_Pilot_Info`
--

CREATE TABLE `v3_Artemis_Pilot_Info` (
  `id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(35) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `pardus_ip` varchar(15) DEFAULT '999.999.999.999',
  `last_received` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Artemis_Pilot_IP`
--

CREATE TABLE `v3_Artemis_Pilot_IP` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip` varchar(15) NOT NULL DEFAULT '999.999.999.999'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Orion_Building_Info`
--

CREATE TABLE `v3_Orion_Building_Info` (
  `bik` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `code` varchar(15) NOT NULL DEFAULT '000000000000000',
  `info` longtext,
  `trade` longtext,
  `special` longtext,
  `added` datetime DEFAULT NULL,
  `info_updated` datetime DEFAULT NULL,
  `stock_updated` datetime DEFAULT NULL,
  `eq_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Orion_Building_Stock`
--

CREATE TABLE `v3_Orion_Building_Stock` (
  `bik` int(11) NOT NULL,
  `name` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `min` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `hide` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Orion_Map_Buildings`
--

CREATE TABLE `v3_Orion_Map_Buildings` (
  `id` int(11) NOT NULL,
  `cluster` varchar(25) DEFAULT NULL,
  `sector` varchar(25) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Orion_Map_Npcs`
--

CREATE TABLE `v3_Orion_Map_Npcs` (
  `id` int(11) NOT NULL,
  `cluster` varchar(25) DEFAULT NULL,
  `sector` varchar(25) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `title` varchar(35) DEFAULT NULL,
  `cloaked` tinyint(4) DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Orion_Npc_Info`
--

CREATE TABLE `v3_Orion_Npc_Info` (
  `id` int(11) NOT NULL,
  `nik` int(11) NOT NULL,
  `code` varchar(15) NOT NULL DEFAULT '000000000000000',
  `info` longtext CHARACTER SET latin1 COLLATE latin1_general_ci,
  `added` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Orion_Pilot_Codes`
--

CREATE TABLE `v3_Orion_Pilot_Codes` (
  `id` int(11) UNSIGNED NOT NULL,
  `sector` varchar(25) NOT NULL,
  `code` varchar(35) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Orion_Pilot_Info`
--

CREATE TABLE `v3_Orion_Pilot_Info` (
  `id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(35) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `pardus_ip` varchar(15) DEFAULT '999.999.999.999',
  `last_received` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Orion_Pilot_IP`
--

CREATE TABLE `v3_Orion_Pilot_IP` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip` varchar(15) NOT NULL DEFAULT '999.999.999.999'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Pardus_Sectors`
--

CREATE TABLE `v3_Pardus_Sectors` (
  `sid` int(11) NOT NULL DEFAULT '0',
  `code` varchar(5) DEFAULT NULL,
  `stat` varchar(5) NOT NULL,
  `cluster` varchar(50) DEFAULT NULL,
  `name` varchar(35) NOT NULL,
  `rows` int(2) NOT NULL,
  `cols` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Pegasus_Building_Info`
--

CREATE TABLE `v3_Pegasus_Building_Info` (
  `bik` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `code` varchar(15) NOT NULL DEFAULT '000000000000000',
  `info` longtext,
  `trade` longtext,
  `special` longtext,
  `added` datetime DEFAULT NULL,
  `info_updated` datetime DEFAULT NULL,
  `stock_updated` datetime DEFAULT NULL,
  `eq_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Pegasus_Building_Stock`
--

CREATE TABLE `v3_Pegasus_Building_Stock` (
  `bik` int(11) NOT NULL,
  `name` varchar(35) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `amount` int(11) NOT NULL DEFAULT '0',
  `min` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `hide` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Pegasus_Map_Buildings`
--

CREATE TABLE `v3_Pegasus_Map_Buildings` (
  `id` int(11) NOT NULL,
  `cluster` varchar(25) DEFAULT NULL,
  `sector` varchar(25) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Pegasus_Map_Npcs`
--

CREATE TABLE `v3_Pegasus_Map_Npcs` (
  `id` int(11) NOT NULL,
  `cluster` varchar(25) DEFAULT NULL,
  `sector` varchar(25) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `title` varchar(35) DEFAULT NULL,
  `cloaked` tinyint(4) DEFAULT NULL,
  `added` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Pegasus_Npc_Info`
--

CREATE TABLE `v3_Pegasus_Npc_Info` (
  `id` int(11) NOT NULL,
  `nik` int(11) NOT NULL,
  `code` varchar(15) NOT NULL DEFAULT '000000000000000',
  `info` longtext CHARACTER SET latin1 COLLATE latin1_general_ci,
  `added` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Pegasus_Pilot_Codes`
--

CREATE TABLE `v3_Pegasus_Pilot_Codes` (
  `id` int(11) UNSIGNED NOT NULL,
  `sector` varchar(25) NOT NULL,
  `code` varchar(35) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Pegasus_Pilot_Info`
--

CREATE TABLE `v3_Pegasus_Pilot_Info` (
  `id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(35) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `pardus_ip` varchar(15) DEFAULT '999.999.999.999',
  `last_received` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `v3_Pegasus_Pilot_IP`
--

CREATE TABLE `v3_Pegasus_Pilot_IP` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip` varchar(15) NOT NULL DEFAULT '999.999.999.999'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `War_Status`
--

CREATE TABLE `War_Status` (
  `Universe` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `WarStatus` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wormholes`
--

CREATE TABLE `wormholes` (
  `id` int(11) NOT NULL,
  `sector` varchar(25) NOT NULL,
  `target` varchar(25) NOT NULL,
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Artemis_Buildings`
--
ALTER TABLE `Artemis_Buildings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Artemis_Maps`
--
ALTER TABLE `Artemis_Maps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Sector` (`sector`) USING BTREE;

--
-- Indexes for table `Artemis_Missions`
--
ALTER TABLE `Artemis_Missions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loc` (`loc`,`id`);

--
-- Indexes for table `Artemis_New_Stock`
--
ALTER TABLE `Artemis_New_Stock`
  ADD PRIMARY KEY (`id`,`name`);

--
-- Indexes for table `Artemis_Npcs`
--
ALTER TABLE `Artemis_Npcs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Artemis_Personal_Resources`
--
ALTER TABLE `Artemis_Personal_Resources`
  ADD PRIMARY KEY (`id`,`loc`);

--
-- Indexes for table `Artemis_Squadrons`
--
ALTER TABLE `Artemis_Squadrons`
  ADD KEY `id` (`id`);

--
-- Indexes for table `Artemis_Stock`
--
ALTER TABLE `Artemis_Stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Artemis_Test_Missions`
--
ALTER TABLE `Artemis_Test_Missions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Artemis_Test_Npcs`
--
ALTER TABLE `Artemis_Test_Npcs`
  ADD PRIMARY KEY (`kid`),
  ADD KEY `deletedFlag` (`deleted`),
  ADD KEY `Cluster` (`cluster`) USING BTREE,
  ADD KEY `Sector` (`sector`) USING BTREE,
  ADD KEY `ID` (`id`);

--
-- Indexes for table `Artemis_Test_Stock`
--
ALTER TABLE `Artemis_Test_Stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Artemis_Users`
--
ALTER TABLE `Artemis_Users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `background`
--
ALTER TABLE `background`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector` (`sector`);

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `connection_log`
--
ALTER TABLE `connection_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Orion_Buildings`
--
ALTER TABLE `Orion_Buildings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Orion_Crew`
--
ALTER TABLE `Orion_Crew`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `Orion_Equipment`
--
ALTER TABLE `Orion_Equipment`
  ADD PRIMARY KEY (`loc`,`name`);

--
-- Indexes for table `Orion_Maps`
--
ALTER TABLE `Orion_Maps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Sector` (`sector`);

--
-- Indexes for table `Orion_Missions`
--
ALTER TABLE `Orion_Missions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Orion_New_Stock`
--
ALTER TABLE `Orion_New_Stock`
  ADD PRIMARY KEY (`id`,`name`);

--
-- Indexes for table `Orion_Npcs`
--
ALTER TABLE `Orion_Npcs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Orion_Personal_Resources`
--
ALTER TABLE `Orion_Personal_Resources`
  ADD PRIMARY KEY (`id`,`loc`),
  ADD KEY `Pilot` (`id`);

--
-- Indexes for table `Orion_Squadrons`
--
ALTER TABLE `Orion_Squadrons`
  ADD KEY `id` (`id`);

--
-- Indexes for table `Orion_Stock`
--
ALTER TABLE `Orion_Stock`
  ADD PRIMARY KEY (`id`,`name`);

--
-- Indexes for table `Orion_Test_Missions`
--
ALTER TABLE `Orion_Test_Missions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Cluster` (`cluster`),
  ADD KEY `Sector` (`sector`);

--
-- Indexes for table `Orion_Test_Npcs`
--
ALTER TABLE `Orion_Test_Npcs`
  ADD PRIMARY KEY (`kid`),
  ADD KEY `Sector` (`sector`),
  ADD KEY `ID` (`id`),
  ADD KEY `deletedFlag` (`deleted`),
  ADD KEY `Cluster` (`cluster`) USING BTREE;

--
-- Indexes for table `Orion_Test_Stock`
--
ALTER TABLE `Orion_Test_Stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Sector` (`sector`);

--
-- Indexes for table `Orion_Users`
--
ALTER TABLE `Orion_Users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `Pardus_Buildings_Data`
--
ALTER TABLE `Pardus_Buildings_Data`
  ADD PRIMARY KEY (`image`);

--
-- Indexes for table `Pardus_Clusters`
--
ALTER TABLE `Pardus_Clusters`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `Pardus_Hash`
--
ALTER TABLE `Pardus_Hash`
  ADD PRIMARY KEY (`function`);

--
-- Indexes for table `Pardus_Maps`
--
ALTER TABLE `Pardus_Maps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Pardus_Npcs`
--
ALTER TABLE `Pardus_Npcs`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `Pardus_Res_Data`
--
ALTER TABLE `Pardus_Res_Data`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `Pardus_Sectors`
--
ALTER TABLE `Pardus_Sectors`
  ADD PRIMARY KEY (`s_id`);

--
-- Indexes for table `Pardus_Ship_Data`
--
ALTER TABLE `Pardus_Ship_Data`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `Pardus_Static_Locations`
--
ALTER TABLE `Pardus_Static_Locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector` (`sector`);

--
-- Indexes for table `Pardus_Upkeep_Data`
--
ALTER TABLE `Pardus_Upkeep_Data`
  ADD PRIMARY KEY (`name`,`res`);

--
-- Indexes for table `Pegasus_Buildings`
--
ALTER TABLE `Pegasus_Buildings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Pegasus_Maps`
--
ALTER TABLE `Pegasus_Maps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Sector` (`sector`) USING BTREE;

--
-- Indexes for table `Pegasus_Squadrons`
--
ALTER TABLE `Pegasus_Squadrons`
  ADD KEY `id` (`id`);

--
-- Indexes for table `Pegasus_Test_Npcs`
--
ALTER TABLE `Pegasus_Test_Npcs`
  ADD PRIMARY KEY (`kid`),
  ADD KEY `Cluster` (`cluster`) USING BTREE,
  ADD KEY `Sector` (`sector`) USING BTREE,
  ADD KEY `id-deleted` (`id`,`deleted`);

--
-- Indexes for table `Pegasus_Test_Stock`
--
ALTER TABLE `Pegasus_Test_Stock`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Pegasus_Users`
--
ALTER TABLE `Pegasus_Users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `v2_Pardus_NPC_Stats`
--
ALTER TABLE `v2_Pardus_NPC_Stats`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `v2_Pardus_Sectors`
--
ALTER TABLE `v2_Pardus_Sectors`
  ADD PRIMARY KEY (`sid`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `v3_Artemis_Building_Info`
--
ALTER TABLE `v3_Artemis_Building_Info`
  ADD PRIMARY KEY (`bik`);

--
-- Indexes for table `v3_Artemis_Building_Stock`
--
ALTER TABLE `v3_Artemis_Building_Stock`
  ADD PRIMARY KEY (`bik`,`name`);

--
-- Indexes for table `v3_Artemis_Map_Buildings`
--
ALTER TABLE `v3_Artemis_Map_Buildings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector` (`sector`);

--
-- Indexes for table `v3_Artemis_Map_Npcs`
--
ALTER TABLE `v3_Artemis_Map_Npcs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector` (`sector`);

--
-- Indexes for table `v3_Artemis_Npc_Info`
--
ALTER TABLE `v3_Artemis_Npc_Info`
  ADD UNIQUE KEY `nik` (`nik`);

--
-- Indexes for table `v3_Artemis_Pilot_Codes`
--
ALTER TABLE `v3_Artemis_Pilot_Codes`
  ADD KEY `id` (`id`);

--
-- Indexes for table `v3_Artemis_Pilot_Info`
--
ALTER TABLE `v3_Artemis_Pilot_Info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `v3_Artemis_Pilot_IP`
--
ALTER TABLE `v3_Artemis_Pilot_IP`
  ADD KEY `id` (`id`);

--
-- Indexes for table `v3_Orion_Building_Info`
--
ALTER TABLE `v3_Orion_Building_Info`
  ADD PRIMARY KEY (`bik`);

--
-- Indexes for table `v3_Orion_Building_Stock`
--
ALTER TABLE `v3_Orion_Building_Stock`
  ADD PRIMARY KEY (`bik`,`name`);

--
-- Indexes for table `v3_Orion_Map_Buildings`
--
ALTER TABLE `v3_Orion_Map_Buildings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector` (`sector`);

--
-- Indexes for table `v3_Orion_Map_Npcs`
--
ALTER TABLE `v3_Orion_Map_Npcs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector` (`sector`),
  ADD KEY `SingleNPC` (`cluster`,`image`);

--
-- Indexes for table `v3_Orion_Npc_Info`
--
ALTER TABLE `v3_Orion_Npc_Info`
  ADD UNIQUE KEY `nik` (`nik`);

--
-- Indexes for table `v3_Orion_Pilot_Codes`
--
ALTER TABLE `v3_Orion_Pilot_Codes`
  ADD KEY `id` (`id`);

--
-- Indexes for table `v3_Orion_Pilot_Info`
--
ALTER TABLE `v3_Orion_Pilot_Info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `v3_Orion_Pilot_IP`
--
ALTER TABLE `v3_Orion_Pilot_IP`
  ADD KEY `id` (`id`);

--
-- Indexes for table `v3_Pardus_Sectors`
--
ALTER TABLE `v3_Pardus_Sectors`
  ADD PRIMARY KEY (`sid`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `v3_Pegasus_Building_Info`
--
ALTER TABLE `v3_Pegasus_Building_Info`
  ADD PRIMARY KEY (`bik`);

--
-- Indexes for table `v3_Pegasus_Building_Stock`
--
ALTER TABLE `v3_Pegasus_Building_Stock`
  ADD PRIMARY KEY (`bik`,`name`);

--
-- Indexes for table `v3_Pegasus_Map_Buildings`
--
ALTER TABLE `v3_Pegasus_Map_Buildings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector` (`sector`);

--
-- Indexes for table `v3_Pegasus_Map_Npcs`
--
ALTER TABLE `v3_Pegasus_Map_Npcs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector` (`sector`);

--
-- Indexes for table `v3_Pegasus_Npc_Info`
--
ALTER TABLE `v3_Pegasus_Npc_Info`
  ADD UNIQUE KEY `nik` (`nik`);

--
-- Indexes for table `v3_Pegasus_Pilot_Codes`
--
ALTER TABLE `v3_Pegasus_Pilot_Codes`
  ADD KEY `id` (`id`);

--
-- Indexes for table `v3_Pegasus_Pilot_Info`
--
ALTER TABLE `v3_Pegasus_Pilot_Info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `v3_Pegasus_Pilot_IP`
--
ALTER TABLE `v3_Pegasus_Pilot_IP`
  ADD KEY `id` (`id`);

--
-- Indexes for table `wormholes`
--
ALTER TABLE `wormholes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sector` (`sector`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Artemis_Test_Npcs`
--
ALTER TABLE `Artemis_Test_Npcs`
  MODIFY `kid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=824457;

--
-- AUTO_INCREMENT for table `Artemis_Users`
--
ALTER TABLE `Artemis_Users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2149;

--
-- AUTO_INCREMENT for table `connection_log`
--
ALTER TABLE `connection_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63930;

--
-- AUTO_INCREMENT for table `Orion_Test_Npcs`
--
ALTER TABLE `Orion_Test_Npcs`
  MODIFY `kid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1564998;

--
-- AUTO_INCREMENT for table `Orion_Users`
--
ALTER TABLE `Orion_Users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2139;

--
-- AUTO_INCREMENT for table `Pardus_Clusters`
--
ALTER TABLE `Pardus_Clusters`
  MODIFY `c_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `Pegasus_Test_Npcs`
--
ALTER TABLE `Pegasus_Test_Npcs`
  MODIFY `kid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=466302;

--
-- AUTO_INCREMENT for table `Pegasus_Users`
--
ALTER TABLE `Pegasus_Users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=843;

--
-- AUTO_INCREMENT for table `v3_Artemis_Building_Info`
--
ALTER TABLE `v3_Artemis_Building_Info`
  MODIFY `bik` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `v3_Orion_Building_Info`
--
ALTER TABLE `v3_Orion_Building_Info`
  MODIFY `bik` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `v3_Pegasus_Building_Info`
--
ALTER TABLE `v3_Pegasus_Building_Info`
  MODIFY `bik` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
