/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.stylesSet.add('tiles',
[
	{
		name:'Full width',
		element:'div',
		styles:
		{
			'background-color':'#3C0',
			padding:'10px',
			width:'90%',
			margin:'10px 0',
			float:'left',
			clear:'both'
		}
	},
	{
		name:'Half width',
		element:'div',
		styles:
		{
			'background-color':'#3C0',
			padding:'10px',
			width:'40%',
			margin:'10px',
			float:'left'
		}
	},
	{
		name:'Marker: Yellow',
		element:'span',
		styles:{'background-color':'Yellow'}
	},
	{
		name:'Marker: Green',
		element:'span',
		styles:{'background-color':'Lime'}
	},
	{
		name:'Image on Right',
		element:'img',
		attributes:{style:'padding: 5px; margin-left: 5px',border:'2',align:'right'}
	},
	{
		name:'Borderless Table',
		element:'table',
		styles:{'border-style':'hidden','background-color':'#E6E6FA'}
	},
	{
		name:'Square Bulleted List',
		element:'ul',
		styles:{'list-style-type':'square'}
	}
]);
