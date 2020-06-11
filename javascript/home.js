/*
    Copyright (C) 2004-2010 Kestas J. Kuliukas

	This file is part of webDiplomacy.

    webDiplomacy is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    webDiplomacy is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with webDiplomacy.  If not, see <http://www.gnu.org/licenses/>.
 */
// See doc/javascript.txt for information on JavaScript in webDiplomacy

/* 
 * For each game panel on the home page add event handlers so that unrelated game panels and 
 * notices will be hidden/faded when the mouse is over a certain game panel or game link.
 */
function homeGameHighlighter() {
	
	$(".homeGamesStats span[gameID].homeGameTitleBar").map(function () {
		let gameTitleBar = $(this);
		let gameID = gameTitleBar.attr("gameID");
		
		// A function to determine if a given element is of the same game
		let isUnrelated = function(testElement) {
			let testGameID = testElement.attr("gameID");
			return !(testGameID == gameID);
		};
		
		// Unrelated game-panels
		let panelsUnrelated = $("div[gameID].gamePanelHome").find( isUnrelated );
		// Unrelated game-notices
		let noticesUnrelated = $("div.homeNotice").find( isUnrelated );
		
		// Functions to set/unset transparency
		// let fadeOut = function(e) { e.setOpacity(0.5); };
		// let fadeIn = function(e) { e.setOpacity(1.0); };
		//
		// Mouse over the game title bar; hide unrelated notices, fade unrelated game panels
		gameTitleBar.onmouseover = function () {
			// panelsUnrelated.map(fadeOut);
			noticesUnrelated.invoke('hide');
		};
		gameTitleBar.onmouseout = function () {
			// panelsUnrelated.map(fadeIn);
			noticesUnrelated.invoke('show');
		};
		
		// Mouse over any game link; fade unrelated notices, fade unrelated game panels
		$('a[gameID="'+gameID+'"]').map(function() {
			let gameNoticeLink = $(this);
			gameNoticeLink.onmouseover = function() {
				// panelsUnrelated.map(fadeOut);
				// noticesUnrelated.map(fadeOut);
			};
			gameNoticeLink.onmouseout = function() {
				// panelsUnrelated.map(fadeIn);
				// noticesUnrelated.map(fadeIn);
			};
		});
		
	},this);
}
