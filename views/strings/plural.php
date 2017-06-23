<tr class="glsr-string">
	<td class="glsr-string-original">
		<p>{{ msgid }}</p>
		<p>{{ msgid_plural }}</p>
	</td>
	<td class="glsr-string-translation">
		<input type="hidden" name="{{ prefix }}[strings][{{ index }}][id]" value="{{ id }}">
		<input type="text" name="{{ prefix }}[strings][{{ index }}][single]" placeholder="single translation" value="{{ single }}">
		<input type="text" name="{{ prefix }}[strings][{{ index }}][plural]" placeholder="plural translation" value="{{ plural }}">
		<span class="description">{{ desc }}</span>
	</td>
</tr>
