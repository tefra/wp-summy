<?php

/**
 * GreekStemmer.php v1.0
 * This is a port of the javascript stemmer by Georgios (Ntais Georgios.Ntais@eurodyn.com)
 * which was developed for his master thessis at Royal Institute of Technology [KTH], Stockholm Sweden
 * http://www.dsv.su.se/~hercules/papers/Ntais_greek_stemmer_thesis_final.pdf
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @package		Summy
 * @version		$Id: Stemmer.php 88 2013-02-04 13:25:53Z Tefra $
 * @author		Christodoulos Tsoulloftas
 * @copyright	Copyright 2011-2013, http://www.komposta.net
 */
namespace Summy\Filter\Gr;

class Stemmer
{

	/**
	 * Step 1 List
	 * @var array
	 */
	private $step1list = array(
		'ΦΑΓΙΑ' => 'ΦΑ',
		'ΦΑΓΙΟΥ' => 'ΦΑ',
		'ΦΑΓΙΩΝ' => 'ΦΑ',
		'ΣΚΑΓΙΑ' => 'ΣΚΑ',
		'ΣΚΑΓΙΟΥ' => 'ΣΚΑ',
		'ΣΚΑΓΙΩΝ' => 'ΣΚΑ',
		'ΟΛΟΓΙΟΥ' => 'ΟΛΟ',
		'ΟΛΟΓΙΑ' => 'ΟΛΟ',
		'ΟΛΟΓΙΩΝ' => 'ΟΛΟ',
		'ΣΟΓΙΟΥ' => 'ΣΟ',
		'ΣΟΓΙΑ' => 'ΣΟ',
		'ΣΟΓΙΩΝ' => 'ΣΟ',
		'ΤΑΤΟΓΙΑ' => 'ΤΑΤΟ',
		'ΤΑΤΟΓΙΟΥ' => 'ΤΑΤΟ',
		'ΤΑΤΟΓΙΩΝ' => 'ΤΑΤΟ',
		'ΚΡΕΑΣ' => 'ΚΡΕ',
		'ΚΡΕΑΤΟΣ' => 'ΚΡΕ',
		'ΚΡΕΑΤΑ' => 'ΚΡΕ',
		'ΚΡΕΑΤΩΝ' => 'ΚΡΕ',
		'ΠΕΡΑΣ' => 'ΠΕΡ',
		'ΠΕΡΑΤΟΣ' => 'ΠΕΡ',
		'ΠΕΡΑΤΑ' => 'ΠΕΡ',
		'ΠΕΡΑΤΩΝ' => 'ΠΕΡ',
		'ΤΕΡΑΣ' => 'ΤΕΡ',
		'ΤΕΡΑΤΟΣ' => 'ΤΕΡ',
		'ΤΕΡΑΤΑ' => 'ΤΕΡ',
		'ΤΕΡΑΤΩΝ' => 'ΤΕΡ',
		'ΦΩΣ' => 'ΦΩ',
		'ΦΩΤΟΣ' => 'ΦΩ',
		'ΦΩΤΑ' => 'ΦΩ',
		'ΦΩΤΩΝ' => 'ΦΩ',
		'ΚΑΘΕΣΤΩΣ' => 'ΚΑΘΕΣΤ',
		'ΚΑΘΕΣΤΩΤΟΣ' => 'ΚΑΘΕΣΤ',
		'ΚΑΘΕΣΤΩΤΑ' => 'ΚΑΘΕΣΤ',
		'ΚΑΘΕΣΤΩΤΩΝ' => 'ΚΑΘΕΣΤ',
		'ΓΕΓΟΝΟΣ' => 'ΓΕΓΟΝ',
		'ΓΕΓΟΝΟΤΟΣ' => 'ΓΕΓΟΝ',
		'ΓΕΓΟΝΟΤΑ' => 'ΓΕΓΟΝ',
		'ΓΕΓΟΝΟΤΩΝ' => 'ΓΕΓΟΝ'
	);

	/**
	 * Step 1 Regex
	 * @var string
	 */
	private $step1regexp = null;

	/**
	 * Vowels
	 * @var string
	 */
	private $v = '[ΑΕΗΙΟΥΩ]';

	/**
	 * Vowels without Υ
	 * @var string
	 */
	private $v2 = '[ΑΕΗΙΟΩ]';

	/**
	 *
	 */
	public function __construct()
	{
		$this->step1regexp = '/(.*)(' . implode('|', array_keys($this->step1list)) . ')$/u';
	}

	/**
	 * Stemming time
	 * @param string $w
	 * @return string
	 */
	public function filter($w)
	{
		if($w === false || $w == '')
		{
			return false;
		}

		$stem = '';
		$suffix = '';
		$firstch = '';
		$origword = $w;
		$test1 = true;

		//Step1
		if(preg_match($this->step1regexp, $w))
		{
			preg_match($this->step1regexp, $w, $fp);
			$stem = $fp[1];
			$suffix = $fp[2];
			$w = $stem . $this->step1list[$suffix];
			$test1 = false;
		}

		// Step 2a
		$re = '/^(.+?)(ΑΔΕΣ|ΑΔΩΝ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$re = '/(ΟΚ|ΜΑΜ|ΜΑΝ|ΜΠΑΜΠ|ΠΑΤΕΡ|ΓΙΑΓΙ|ΝΤΑΝΤ|ΚΥΡ|ΘΕΙ|ΠΕΘΕΡ)$/u';
			if(!preg_match($re, $w))
			{
				$w = $w . "ΑΔ";
			}
		}

		//Step 2b
		$re = '/^(.+?)(ΕΔΕΣ|ΕΔΩΝ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$exept2 = '/(ΟΠ|ΙΠ|ΕΜΠ|ΥΠ|ΓΗΠ|ΔΑΠ|ΚΡΑΣΠ|ΜΙΛ)$/u';
			if(preg_match($exept2, $w))
			{
				$w = $w . 'ΕΔ';
			}
		}

		//Step 2c
		$re = '/^(.+?)(ΟΥΔΕΣ|ΟΥΔΩΝ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;

			$exept3 = '/(ΑΡΚ|ΚΑΛΙΑΚ|ΠΕΤΑΛ|ΛΙΧ|ΠΛΕΞ|ΣΚ|Σ|ΦΛ|ΦΡ|ΒΕΛ|ΛΟΥΛ|ΧΝ|ΣΠ|ΤΡΑΓ|ΦΕ)$/u';
			if(preg_match($exept3, $w))
			{
				$w = $w . 'ΟΥΔ';
			}
		}

		//Step 2d
		$re = '/^(.+?)(ΕΩΣ|ΕΩΝ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept4 = '/^(Θ|Δ|ΕΛ|ΓΑΛ|Ν|Π|ΙΔ|ΠΑΡ)$/u';
			if(preg_match($exept4, $w))
			{
				$w = $w . 'Ε';
			}
		}

		//Step 3
		$re = '/^(.+?)(ΙΑ|ΙΟΥ|ΙΩΝ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;

			$re = '/' . $this->v . '$/u';
			$test1 = false;
			if(preg_match($re, $w))
			{
				$w = $stem . 'Ι';
			}
		}

		//Step 4
		$re = '/^(.+?)(ΙΚΑ|ΙΚΟ|ΙΚΟΥ|ΙΚΩΝ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;

			$test1 = false;
			$re = '/' . $this->v . '$/';
			$exept5 = '/^(ΑΛ|ΑΔ|ΕΝΔ|ΑΜΑΝ|ΑΜΜΟΧΑΛ|ΗΘ|ΑΝΗΘ|ΑΝΤΙΔ|ΦΥΣ|ΒΡΩΜ|ΓΕΡ|ΕΞΩΔ|ΚΑΛΠ|ΚΑΛΛΙΝ|ΚΑΤΑΔ|ΜΟΥΛ|ΜΠΑΝ|ΜΠΑΓΙΑΤ|ΜΠΟΛ|ΜΠΟΣ|ΝΙΤ|ΞΙΚ|ΣΥΝΟΜΗΛ|ΠΕΤΣ|ΠΙΤΣ|ΠΙΚΑΝΤ|ΠΛΙΑΤΣ|ΠΟΣΤΕΛΝ|ΠΡΩΤΟΔ|ΣΕΡΤ|ΣΥΝΑΔ|ΤΣΑΜ|ΥΠΟΔ|ΦΙΛΟΝ|ΦΥΛΟΔ|ΧΑΣ)$/u';
			if(preg_match($re, $w) || preg_match($exept5, $w))
			{
				$w = $w . 'ΙΚ';
			}
		}

		//step 5a
		$re = '/^(.+?)(ΑΜΕ)$/u';
		$re2 = '/^(.+?)(ΑΓΑΜΕ|ΗΣΑΜΕ|ΟΥΣΑΜΕ|ΗΚΑΜΕ|ΗΘΗΚΑΜΕ)$/u';
		if($w == "ΑΓΑΜΕ")
		{
			$w = "ΑΓΑΜ";
		}

		if(preg_match($re2, $w))
		{
			preg_match($re2, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;
		}

		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept6 = '/^(ΑΝΑΠ|ΑΠΟΘ|ΑΠΟΚ|ΑΠΟΣΤ|ΒΟΥΒ|ΞΕΘ|ΟΥΛ|ΠΕΘ|ΠΙΚΡ|ΠΟΤ|ΣΙΧ|Χ)$/u';
			if(preg_match($exept6, $w))
			{
				$w = $w . "ΑΜ";
			}
		}

		//Step 5b
		$re2 = '/^(.+?)(ΑΝΕ)$/';
		$re3 = '/^(.+?)(ΑΓΑΝΕ|ΗΣΑΝΕ|ΟΥΣΑΝΕ|ΙΟΝΤΑΝΕ|ΙΟΤΑΝΕ|ΙΟΥΝΤΑΝΕ|ΟΝΤΑΝΕ|ΟΤΑΝΕ|ΟΥΝΤΑΝΕ|ΗΚΑΝΕ|ΗΘΗΚΑΝΕ)$/u';

		if(preg_match($re3, $w))
		{
			preg_match($re3, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$re3 = '/^(ΤΡ|ΤΣ)$/u';
			if(preg_match($re3, $w))
			{
				$w = $w . "ΑΓΑΝ";
			}
		}

		if(preg_match($re2, $w))
		{
			preg_match($re2, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$re2 = '/' . $this->v2 . '$/u';
			$exept7 = '/^(ΒΕΤΕΡ|ΒΟΥΛΚ|ΒΡΑΧΜ|Γ|ΔΡΑΔΟΥΜ|Θ|ΚΑΛΠΟΥΖ|ΚΑΣΤΕΛ|ΚΟΡΜΟΡ|ΛΑΟΠΛ|ΜΩΑΜΕΘ|Μ|ΜΟΥΣΟΥΛΜ|Ν|ΟΥΛ|Π|ΠΕΛΕΚ|ΠΛ|ΠΟΛΙΣ|ΠΟΡΤΟΛ|ΣΑΡΑΚΑΤΣ|ΣΟΥΛΤ|ΤΣΑΡΛΑΤ|ΟΡΦ|ΤΣΙΓΓ|ΤΣΟΠ|ΦΩΤΟΣΤΕΦ|Χ|ΨΥΧΟΠΛ|ΑΓ|ΟΡΦ|ΓΑΛ|ΓΕΡ|ΔΕΚ|ΔΙΠΛ|ΑΜΕΡΙΚΑΝ|ΟΥΡ|ΠΙΘ|ΠΟΥΡΙΤ|Σ|ΖΩΝΤ|ΙΚ|ΚΑΣΤ|ΚΟΠ|ΛΙΧ|ΛΟΥΘΗΡ|ΜΑΙΝΤ|ΜΕΛ|ΣΙΓ|ΣΠ|ΣΤΕΓ|ΤΡΑΓ|ΤΣΑΓ|Φ|ΕΡ|ΑΔΑΠ|ΑΘΙΓΓ|ΑΜΗΧ|ΑΝΙΚ|ΑΝΟΡΓ|ΑΠΗΓ|ΑΠΙΘ|ΑΤΣΙΓΓ|ΒΑΣ|ΒΑΣΚ|ΒΑΘΥΓΑΛ|ΒΙΟΜΗΧ|ΒΡΑΧΥΚ|ΔΙΑΤ|ΔΙΑΦ|ΕΝΟΡΓ|ΘΥΣ|ΚΑΠΝΟΒΙΟΜΗΧ|ΚΑΤΑΓΑΛ|ΚΛΙΒ|ΚΟΙΛΑΡΦ|ΛΙΒ|ΜΕΓΛΟΒΙΟΜΗΧ|ΜΙΚΡΟΒΙΟΜΗΧ|ΝΤΑΒ|ΞΗΡΟΚΛΙΒ|ΟΛΙΓΟΔΑΜ|ΟΛΟΓΑΛ|ΠΕΝΤΑΡΦ|ΠΕΡΗΦ|ΠΕΡΙΤΡ|ΠΛΑΤ|ΠΟΛΥΔΑΠ|ΠΟΛΥΜΗΧ|ΣΤΕΦ|ΤΑΒ|ΤΕΤ|ΥΠΕΡΗΦ|ΥΠΟΚΟΠ|ΧΑΜΗΛΟΔΑΠ|ΨΗΛΟΤΑΒ)$/u';
			if(preg_match($re2, $w) || preg_match($exept7, $w))
			{
				$w = $w . "ΑΝ";
			}
		}

		//Step 5c
		$re3 = '/^(.+?)(ΕΤΕ)$/u';
		$re4 = '/^(.+?)(ΗΣΕΤΕ)$/u';

		if(preg_match($re4, $w))
		{
			preg_match($re4, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;
		}

		if(preg_match($re3, $w))
		{
			preg_match($re3, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			//$re3 = $this->v2 . '$';
			$re3 = '/' . $this->v2 . '$/u';

			$exept8 = '/(ΟΔ|ΑΙΡ|ΦΟΡ|ΤΑΘ|ΔΙΑΘ|ΣΧ|ΕΝΔ|ΕΥΡ|ΤΙΘ|ΥΠΕΡΘ|ΡΑΘ|ΕΝΘ|ΡΟΘ|ΣΘ|ΠΥΡ|ΑΙΝ|ΣΥΝΔ|ΣΥΝ|ΣΥΝΘ|ΧΩΡ|ΠΟΝ|ΒΡ|ΚΑΘ|ΕΥΘ|ΕΚΘ|ΝΕΤ|ΡΟΝ|ΑΡΚ|ΒΑΡ|ΒΟΛ|ΩΦΕΛ)$/u';
			$exept9 = '/^(ΑΒΑΡ|ΒΕΝ|ΕΝΑΡ|ΑΒΡ|ΑΔ|ΑΘ|ΑΝ|ΑΠΛ|ΒΑΡΟΝ|ΝΤΡ|ΣΚ|ΚΟΠ|ΜΠΟΡ|ΝΙΦ|ΠΑΓ|ΠΑΡΑΚΑΛ|ΣΕΡΠ|ΣΚΕΛ|ΣΥΡΦ|ΤΟΚ|Υ|Δ|ΕΜ|ΘΑΡΡ|Θ)$/u';

			if(preg_match($re3, $w) || preg_match($exept8, $w) || preg_match($exept9, $w))
			{
				$w = $w . "ΕΤ";
			}
		}

		//Step 5d
		$re = '/^(.+?)(ΟΝΤΑΣ|ΩΝΤΑΣ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept10 = '/^(ΑΡΧ)$/u';
			$exept11 = '/(ΚΡΕ)$/u';
			if(preg_match($exept10, $w))
			{
				$w = $w . "ΟΝΤ";
			}
			if(preg_match($exept11, $w))
			{
				$w = $w . "ΩΝΤ";
			}
		}

		//Step 5e
		$re = '/^(.+?)(ΟΜΑΣΤΕ|ΙΟΜΑΣΤΕ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept11 = '/^(ΟΝ)$/u';
			if(preg_match($exept11, $w))
			{
				$w = $w . "ΟΜΑΣΤ";
			}
		}

		//Step 5f
		$re = '/^(.+?)(ΕΣΤΕ)$/u';
		$re2 = '/^(.+?)(ΙΕΣΤΕ)$/u';

		if(preg_match($re2, $w))
		{
			preg_match($re2, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$re2 = '/^(Π|ΑΠ|ΣΥΜΠ|ΑΣΥΜΠ|ΑΚΑΤΑΠ|ΑΜΕΤΑΜΦ)$/u';
			if(preg_match($re2, $w))
			{
				$w = $w . "ΙΕΣΤ";
			}
		}

		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept12 = '/^(ΑΛ|ΑΡ|ΕΚΤΕΛ|Ζ|Μ|Ξ|ΠΑΡΑΚΑΛ|ΑΡ|ΠΡΟ|ΝΙΣ)$/u';
			if(preg_match($exept12, $w))
			{
				$w = $w . "ΕΣΤ";
			}
		}

		//Step 5g
		$re = '/^(.+?)(ΗΚΑ|ΗΚΕΣ|ΗΚΕ)$/u';
		$re2 = '/^(.+?)(ΗΘΗΚΑ|ΗΘΗΚΕΣ|ΗΘΗΚΕ)$/u';

		if(preg_match($re2, $w))
		{
			preg_match($re2, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;
		}

		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept13 = '/(ΣΚΩΛ|ΣΚΟΥΛ|ΝΑΡΘ|ΣΦ|ΟΘ|ΠΙΘ)$/u';
			$exept14 = '/^(ΔΙΑΘ|Θ|ΠΑΡΑΚΑΤΑΘ|ΠΡΟΣΘ|ΣΥΝΘ|)$/u';
			if(preg_match($exept13, $w) || preg_match($exept14, $w))
			{
				$w = $w . "ΗΚ";
			}
		}

		//Step 5h
		$re = '/^(.+?)(ΟΥΣΑ|ΟΥΣΕΣ|ΟΥΣΕ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept15 = '/^(ΦΑΡΜΑΚ|ΧΑΔ|ΑΓΚ|ΑΝΑΡΡ|ΒΡΟΜ|ΕΚΛΙΠ|ΛΑΜΠΙΔ|ΛΕΧ|Μ|ΠΑΤ|Ρ|Λ|ΜΕΔ|ΜΕΣΑΖ|ΥΠΟΤΕΙΝ|ΑΜ|ΑΙΘ|ΑΝΗΚ|ΔΕΣΠΟΖ|ΕΝΔΙΑΦΕΡ|ΔΕ|ΔΕΥΤΕΡΕΥ|ΚΑΘΑΡΕΥ|ΠΛΕ|ΤΣΑ)$/u';
			$exept16 = '/(ΠΟΔΑΡ|ΒΛΕΠ|ΠΑΝΤΑΧ|ΦΡΥΔ|ΜΑΝΤΙΛ|ΜΑΛΛ|ΚΥΜΑΤ|ΛΑΧ|ΛΗΓ|ΦΑΓ|ΟΜ|ΠΡΩΤ)$/u';
			if(preg_match($exept15, $w) || preg_match($exept16, $w))
			{
				$w = $w . "ΟΥΣ";
			}
		}

		//Step 5i
		$re = '/^(.+?)(ΑΓΑ|ΑΓΕΣ|ΑΓΕ)$/u';

		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept17 = '/^(ΨΟΦ|ΝΑΥΛΟΧ)$/u';
			$exept20 = '/(ΚΟΛΛ)$/u';
			$exept18 = '/^(ΑΒΑΣΤ|ΠΟΛΥΦ|ΑΔΗΦ|ΠΑΜΦ|Ρ|ΑΣΠ|ΑΦ|ΑΜΑΛ|ΑΜΑΛΛΙ|ΑΝΥΣΤ|ΑΠΕΡ|ΑΣΠΑΡ|ΑΧΑΡ|ΔΕΡΒΕΝ|ΔΡΟΣΟΠ|ΞΕΦ|ΝΕΟΠ|ΝΟΜΟΤ|ΟΛΟΠ|ΟΜΟΤ|ΠΡΟΣΤ|ΠΡΟΣΩΠΟΠ|ΣΥΜΠ|ΣΥΝΤ|Τ|ΥΠΟΤ|ΧΑΡ|ΑΕΙΠ|ΑΙΜΟΣΤ|ΑΝΥΠ|ΑΠΟΤ|ΑΡΤΙΠ|ΔΙΑΤ|ΕΝ|ΕΠΙΤ|ΚΡΟΚΑΛΟΠ|ΣΙΔΗΡΟΠ|Λ|ΝΑΥ|ΟΥΛΑΜ|ΟΥΡ|Π|ΤΡ|Μ)$/u';
			$exept19 = '/(ΟΦ|ΠΕΛ|ΧΟΡΤ|ΛΛ|ΣΦ|ΡΠ|ΦΡ|ΠΡ|ΛΟΧ|ΣΜΗΝ)$/u';

			if((preg_match($exept18, $w) || preg_match($exept19, $w)) && !(preg_match($exept17, $w) || preg_match($exept20, $w)))
			{
				$w = $w . "ΑΓ";
			}
		}

		//Step 5j
		$re = '/^(.+?)(ΗΣΕ|ΗΣΟΥ|ΗΣΑ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept21 = '/^(Ν|ΧΕΡΣΟΝ|ΔΩΔΕΚΑΝ|ΕΡΗΜΟΝ|ΜΕΓΑΛΟΝ|ΕΠΤΑΝ)$/u';
			if(preg_match($exept21, $w))
			{
				$w = $w . "ΗΣ";
			}
		}

		//Step 5k
		$re = '/^(.+?)(ΗΣΤΕ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept22 = '/^(ΑΣΒ|ΣΒ|ΑΧΡ|ΧΡ|ΑΠΛ|ΑΕΙΜΝ|ΔΥΣΧΡ|ΕΥΧΡ|ΚΟΙΝΟΧΡ|ΠΑΛΙΜΨ)$/u';
			if(preg_match($exept22, $w))
			{
				$w = $w . "ΗΣΤ";
			}
		}

		//Step 5l
		$re = '/^(.+?)(ΟΥΝΕ|ΗΣΟΥΝΕ|ΗΘΟΥΝΕ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept23 = '/^(Ν|Ρ|ΣΠΙ|ΣΤΡΑΒΟΜΟΥΤΣ|ΚΑΚΟΜΟΥΤΣ|ΕΞΩΝ)$/u';
			if(preg_match($exept23, $w))
			{
				$w = $w . "ΟΥΝ";
			}
		}

		//Step 5l
		$re = '/^(.+?)(ΟΥΜΕ|ΗΣΟΥΜΕ|ΗΘΟΥΜΕ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
			$test1 = false;

			$exept24 = '/^(ΠΑΡΑΣΟΥΣ|Φ|Χ|ΩΡΙΟΠΛ|ΑΖ|ΑΛΛΟΣΟΥΣ|ΑΣΟΥΣ)$/u';
			if(preg_match($exept24, $w))
			{
				$w = $w . "ΟΥΜ";
			}
		}

		// Step 6
		$re = '/^(.+?)(ΜΑΤΑ|ΜΑΤΩΝ|ΜΑΤΟΣ)$/u';
		$re2 = '/^(.+?)(Α|ΑΓΑΤΕ|ΑΓΑΝ|ΑΕΙ|ΑΜΑΙ|ΑΝ|ΑΣ|ΑΣΑΙ|ΑΤΑΙ|ΑΩ|Ε|ΕΙ|ΕΙΣ|ΕΙΤΕ|ΕΣΑΙ|ΕΣ|ΕΤΑΙ|Ι|ΙΕΜΑΙ|ΙΕΜΑΣΤΕ|ΙΕΤΑΙ|ΙΕΣΑΙ|ΙΕΣΑΣΤΕ|ΙΟΜΑΣΤΑΝ|ΙΟΜΟΥΝ|ΙΟΜΟΥΝΑ|ΙΟΝΤΑΝ|ΙΟΝΤΟΥΣΑΝ|ΙΟΣΑΣΤΑΝ|ΙΟΣΑΣΤΕ|ΙΟΣΟΥΝ|ΙΟΣΟΥΝΑ|ΙΟΤΑΝ|ΙΟΥΜΑ|ΙΟΥΜΑΣΤΕ|ΙΟΥΝΤΑΙ|ΙΟΥΝΤΑΝ|Η|ΗΔΕΣ|ΗΔΩΝ|ΗΘΕΙ|ΗΘΕΙΣ|ΗΘΕΙΤΕ|ΗΘΗΚΑΤΕ|ΗΘΗΚΑΝ|ΗΘΟΥΝ|ΗΘΩ|ΗΚΑΤΕ|ΗΚΑΝ|ΗΣ|ΗΣΑΝ|ΗΣΑΤΕ|ΗΣΕΙ|ΗΣΕΣ|ΗΣΟΥΝ|ΗΣΩ|Ο|ΟΙ|ΟΜΑΙ|ΟΜΑΣΤΑΝ|ΟΜΟΥΝ|ΟΜΟΥΝΑ|ΟΝΤΑΙ|ΟΝΤΑΝ|ΟΝΤΟΥΣΑΝ|ΟΣ|ΟΣΑΣΤΑΝ|ΟΣΑΣΤΕ|ΟΣΟΥΝ|ΟΣΟΥΝΑ|ΟΤΑΝ|ΟΥ|ΟΥΜΑΙ|ΟΥΜΑΣΤΕ|ΟΥΝ|ΟΥΝΤΑΙ|ΟΥΝΤΑΝ|ΟΥΣ|ΟΥΣΑΝ|ΟΥΣΑΤΕ|Υ|ΥΣ|Ω|ΩΝ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem . "ΜΑ";
		}

		if(preg_match($re2, $w) && $test1)
		{
			preg_match($re2, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
		}

		// Step 7 (ΠΑΡΑΘΕΤΙΚΑ)
		$re = '/^(.+?)(ΕΣΤΕΡ|ΕΣΤΑΤ|ΟΤΕΡ|ΟΤΑΤ|ΥΤΕΡ|ΥΤΑΤ|ΩΤΕΡ|ΩΤΑΤ)$/u';
		if(preg_match($re, $w))
		{
			preg_match($re, $w, $fp);
			$stem = $fp[1];
			$w = $stem;
		}
		return $w;
	}
}

?>