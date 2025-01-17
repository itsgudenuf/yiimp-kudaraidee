<?php

/* A REST/JSON API IS NOT SUPPOSED TO RETURN HTML AND CSS! MORONS!!!! */
function strip_data($data)
{
	$out = strip_tags($data);
	$out = preg_replace("#[\t ]+#", " ", $out);
	$out = preg_replace("# [\r\n]+#", "\n", $out);
	$out = preg_replace("#[\r\n]+#", "\n", $out);
	if (strpos($out, 'CloudFlare') !== false) $out = 'CloudFlare error';
	if (strpos($out, 'DDoS protection by Cloudflare') !== false) $out = 'CloudFlare error';
	if (strpos($out, '500 Error') !== false) $out = '500 Error';
	return $out;
}

require_once("bitstamp.php");
require_once("bittrex.php");
require_once("bitz.php");
require_once("bleutrade.php");
require_once("cexio.php");
require_once("crex24.php");
require_once("deliondex.php");
require_once("exbitron.php");
require_once("escodex.php");
require_once("gateio.php");
require_once("graviex.php");
require_once("kraken.php");
require_once("poloniex.php");
require_once("yobit.php");
require_once("shapeshift.php");
require_once("bter.php");
require_once("empoex.php");
require_once("jubi.php");
require_once("alcurex.php");
require_once("binance.php");
require_once("hitbtc.php");
require_once("kucoin.php");
require_once("livecoin.php");
require_once("cryptowatch.php");
require_once("stocksexchange.php");
require_once("tradeogre.php");
require_once("tradesatoshi.php");
require_once("txbit.php");
require_once("swiftex.php");
require_once("unnamed.php");
require_once("bibox.php");
require_once("altilly.php");


/* Format an exchange coin Url */
function getMarketUrl($coin, $marketName)
{
	$symbol = $coin->getOfficialSymbol();
	$lowsymbol = strtolower($symbol);
	$base = 'BTC';

	$market = trim($marketName);
	if (strpos($marketName, ' ')) {
		$parts = explode(' ',$marketName);
		$market = $parts[0];
		$base = $parts[1];
		if (empty($base)) {
			debuglog("warning: invalid market name '$marketName'");
			$base = dboscalar(
			"SELECT base_coin FROM markets WHERE coinid=:id AND name=:name", array(
				':id'=>$coin->id, ':name'=>$marketName,
			));
		}
	}

	$lowbase = strtolower($base);

	if($market == 'cryptowatch') {
		$exchange = 'poloniex'; // default for big altcoins
		// and for most big btc fiat prices :
		if(in_array($symbol, array('EUR','CAD','GBP')))
			$exchange = 'kraken';
		elseif(in_array($symbol, array('AUD','CNY','JPY')))
			$exchange = 'quoine';
		elseif(in_array($symbol, array('USD')))
			$exchange = 'bitfinex';
	}

	if($market == 'alcurex')
		$url = "https://alcurex.com/#{$symbol}-{$base}";
	else if($market == 'altilly')
		$url = "https://altilly.com/market/{$symbol}_{$base}";
	else if($market == 'bibox')
		$url = "https://www.bibox.com/exchange?coinPair={$symbol}_{$base}";
	else if($market == 'binance')
		$url = "https://www.binance.com/trade.html?symbol={$symbol}_{$base}";
	else if($market == 'bittrex')
		$url = "https://bittrex.com/Market/Index?MarketName={$base}-{$symbol}";
	else if($market == 'bitz')
		$url = "https://www.bit-z.com/exchange/{$symbol}_{$base}";
	else if($market == 'poloniex')
		$url = "https://poloniex.com/exchange#{$lowbase}_{$lowsymbol}";
	else if($market == 'bleutrade')
		$url = "https://bleutrade.com/exchange/{$symbol}/{$base}";
	else if($market == 'bter')
		$url = "https://bter.com/trade/{$lowsymbol}_{$lowbase}";
	else if($market == 'cexio')
		$url = "https://cex.io/trade/{$symbol}-{$base}";
	else if($market == 'crex24')
		$url = "https://crex24.com/exchange/{$symbol}-{$base}";
	else if($market == 'cryptowatch')
		$url = "https://cryptowat.ch/{$exchange}/{$lowbase}{$lowsymbol}";
	else if($market == 'c-cex')
		$url = "https://c-cex.com/?p={$lowsymbol}-{$lowbase}";
	else if($market == 'empoex')
		$url = "http://www.empoex.com/trade/{$symbol}-{$base}";
	else if($market == 'deliondex')
		$url = "https://dex.delion.online/market/DELION.{$symbol}_DELION.{$base}";
	else if($market == 'exbitron')
		$url = "https://www.exbitron.com/trading/{$symbol}{$base}";
	else if($market == 'escodex')
		$url = "https://wallet.escodex.com/market/ESCODEX.{$symbol}_ESCODEX.{$base}";
	else if($market == 'gateio')
		$url = "https://gate.io/trade/{$symbol}_{$base}";
	else if($market == 'graviex')
		$url = "https://graviex.net/markets/{$lowsymbol}{$lowbase}";
	else if($market == 'jubi')
		$url = "http://jubi.com/coin/{$lowsymbol}";
	else if($market == 'hitbtc')
		$url = "https://hitbtc.com/exchange/{$symbol}-to-{$base}";
	else if($market == 'kucoin')
		$url = "https://www.kucoin.com/#/trade.pro/{$symbol}-{$base}";
	else if($market == 'livecoin')
		$url = "https://www.livecoin.net/trade/?currencyPair={$symbol}%2F{$base}";
	else if($market == 'stocksexchange')
		$url = "https://stocks.exchange/trade/$symbol/$base";
	else if($market == 'tradeogre')
		$url = "https://tradeogre.com/exchange/{$base}-{$symbol}";
	else if($market == 'tradesatoshi')
		$url = "https://tradesatoshi.com/Exchange?market={$symbol}_{$base}";
	else if($market == 'yobit')
		$url = "https://yobit.net/en/trade/{$symbol}/{$base}";
	else if($market == 'swiftex')
		$url = "https://swiftex.co/trading/{$lowsymbol}-{$lowbase}";	
	else if($market == 'unnamed')
		$url = "https://www.unnamed.exchange/Exchange/Basic?market={$symbol}_{$base}";	
	else
		$url = "";

	return $url;
}

// $market can be a db_markets or a string (symbol)
function exchange_update_market($exchange, $market)
{
	$fn_update = str_replace('-','',$exchange.'_update_market');
	if (function_exists($fn_update)) {
		return $fn_update($market);
	} else {
		debuglog(__FUNCTION__.': '.$fn_update.'() not implemented');
		user()->setFlash('error', $fn_update.'() not yet implemented');
		return false;
	}
}

// used to manually update one market price
function exchange_update_market_by_id($idmarket)
{
	$market = getdbo('db_markets', $idmarket);
	if (!$market) return false;

	return exchange_update_market($market->name, $market);
}
