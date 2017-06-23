<tr class="glsr-string">
	<td class="glsr-string-original">
		<p>{{ msgid }}</p>
	</td>
	<td class="glsr-string-translation">
		<input type="hidden" name="{{ prefix }}[strings][{{ index }}][id]" value="{{ id }}">
		<textarea rows="2" name="{{ prefix }}[strings][{{ index }}][single]">{{ single }}</textarea>
		<span class="description">{{ desc }}</span>
	</td>
</tr>
